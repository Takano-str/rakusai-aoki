<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Consumer;
// use App\Models\ConsumerStatusHistory;
use App\Models\ConsumerSchedule;
use App\Models\InterviewInfoForSpreadsheet;
use App\Models\Remind;
use App\Models\Schedule;
use App\Models\Worksheet;
use App\Services\Aws\LambdaService;
use App\Services\Util\CreateUrlService;
use App\Services\Util\ShapeTextService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;

class ConsumerAnswerController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \Log::info('========== ConsumerAnswerController START ==========');
        \Log::info($request);

        $this->consumerId = $request->get('consumerId', 0);
        $this->answerData = $request->get('answerData', []);
        $this->scheduleIds = $this->answerData['StepInterviewSchedule'] ?? [];
        $this->anotherScheduleDates = $this->answerData['StepInterviewAnotherSchedule'] ?? [];
        $isAnotherAdjust = $this->answerData['isAnotherAdjust'] ?? false;
        $isFailure = $this->answerData['isFailure'] ?? false;
        $interviewVenue = $this->answerData['interviewPlace'] ?? 'Web面接';

        $consumer = Consumer::with('consumerDetail')
            ->with('worksheet')
            ->where('id', $this->consumerId)
            ->first()
            ->toArray();
        // \Log::debug($consumer);

        $this->atsId = $consumer['ats_id'] ?? '';
        $this->storeId = $consumer['store_id'] ?? 1;
        $worksheetId = $consumer['worksheet']['id'];
        $consumerName = $consumer['consumer_detail']['name'] ?? '登録されていません';
        $consumerTel = $consumer['consumer_detail']['tel'] ?? '登録されていません';
        $consumerMail = $consumer['consumer_detail']['mail'] ?? '登録されていません';

        // 回答情報の保存
        Worksheet::where('id', $worksheetId)
            ->update([
                'worksheet_answer' => json_encode($this->answerData, JSON_UNESCAPED_UNICODE),
            ]);

        // 年齢チェック
        // $birthdate = $this->answerData[''];

        $flow = '';
        if ($isAnotherAdjust) {
            // 店舗日程調整
            $flow = 'schdules_off';
        } elseif ($isFailure) {
            // 不合格
            $flow = 'failure';
        } else {
            // 面接確定
            $flow = 'schdules_on';
        }

        // ステータス更新
        /** @todo **/


        switch ($flow) {
            case 'failure':
                //　リマインド登録
                Remind::create([
                    'consumer_id' => $this->consumerId,
                    'message_type' => 'pass',
                    'send_text' => '',
                    'send_time' => '',
                ]);
                break;

            case 'schdules_off':
                /** ====== 日程調整の場合 ====== **/
                // 店舗へメール送信
                // アンケートURL生成
                $worksheetStoreUrl = CreateUrlService::makeWorksheetUrl(
                    'worksheetStore',
                    ['csid' => Crypt::encrypt($this->consumerId),]
                );
                Worksheet::where('id', $worksheetId)
                    ->update([
                        'store_url' => $worksheetStoreUrl,
                    ]);
                $worksheetStoreMessage =  ShapeTextService::makeSendText(
                    'schedule_adjustment',
                    $worksheetStoreUrl,
                    [
                        'schedule_adjust_dates' => implode("\n", $this->anotherScheduleDates),
                        'consumer_name' => $consumerName,
                        // 'tel' => $consumer['consumer_detail']['tel'] ?? '登録されていません',
                        // 'mail' => $consumer['consumer_detail']['name'] ?? '登録されていません',
                    ]
                );
                \Log::debug('先方へメール!!!!!');
                // Lambda起動
                $customerMail = Company::find(1)->mail;
                LambdaService::apiRequest('common-mail', [
                    'to_address'   => [$customerMail],
                    'cc_address'   => config('aws.lambda.store_adjust.cc_address', []),
                    'bcc_address'  => config('aws.lambda.store_adjust.bcc_address', ['akiyama-d@dym.jp']),
                    'from_address' => config('aws.lambda.store_adjust.from_address'),
                    'subject'      => config('aws.lambda.store_adjust.subject'),
                    'message'      => nl2br($worksheetStoreMessage),
                ]);
                LambdaService::apiRequest('nx-careerroad-status-switcher', [
                    'status' => [
                        [
                            "applicant_id" => $this->atsId,
                            "next_status" => "スケジュール選択待ち",
                        ]
                    ]
                ]);
                break;

            case 'schdules_on':
                /** ====== 面接確定の場合 ====== **/
                $this->schedule = [];
                $this->scheduleDecideId = '';
                $this->scheduleType = '';
                $this->scheduleCapacity = '';
                $this->scheduleBookingCount = '';
                $this->scheduleInterviewVenue = '';
                $this->venueAddress = '';
                foreach ($this->scheduleIds as $scheduleId) {
                    $targetSchedule = Schedule::where('id', $scheduleId)
                        ->whereIn('type', [
                            config('schedule.type.empty.value'),
                            config('schedule.type.jobop_available.value'),
                        ])
                        ->first();
                    if (!empty($targetSchedule)) {
                        $this->schedule = $targetSchedule->toArray();
                        $this->scheduleDecideId = $this->schedule['id'];
                        $this->scheduleType = $this->schedule['type'];
                        $this->scheduleBookingCount = $this->schedule['booking_count'];
                        $this->scheduleInterviewVenue = $this->schedule['interview_venue'];
                        break;
                    }
                }
                \Log::debug($this->schedule);

                // 面接日時の取得
                $interviewStartTime     = new Carbon($this->schedule['start_date']);
                $interviewStartCarbon   = clone $interviewStartTime;
                $interviewStartStr      = $interviewStartCarbon->format('Y/m/d H:i');

                /** 面接場所の住所の取得 @todo **/
                $venueAddressList = config('venueAddressList');
                foreach ($venueAddressList as $keyword => $venueAddress) {
                    if (strpos($this->scheduleInterviewVenue, $keyword) !== false) {
                        $this->venueAddress = $venueAddress;
                        break;
                    }
                }

                // ショートURL生成
                $worksheetStatusUrl = CreateUrlService::makeWorksheetUrl(
                    'worksheetStatus',
                    ['csid' => Crypt::encrypt($this->consumerId),]
                );
                Worksheet::where('id', $worksheetId)
                    ->update([
                        'status_url' => $worksheetStatusUrl,
                    ]);

                // 面接日時の確定処理

                /** @todo 判定甘いので後で直す **/ 
                $googleMeetUrls = [];
                $googleMeetUrlStr = "";
                if (strpos($interviewVenue, "web") !== false) {
                    // Web面接の場合
                    $googleMeetUrls = config('venueGoogleMeetUrlList')[$interviewVenue] ?? [];
                    // $googleMeetUrlStr = $googleMeetUrls[0] . "\n";
                    $googleMeetUrlStr = 'https://meet.google.com/bhq-cpfq-hmk';
                }

                // 確定の案内の文言を作成
                $scheduleDescription = ShapeTextService::makeSendText(
                    'scheduleDescription',
                    $worksheetStatusUrl,
                    [
                        'name' => $consumerName,
                        'tel' => $consumerTel,
                        'mail' => $consumerMail,
                        // 'start' => (new Carbon($this->schedule['start_date']))->format('Y-m-d H:i'),
                        // 'end' => (new Carbon($this->schedule['end_date']))->format('Y-m-d H:i'),
                        'start' => (new Carbon($this->schedule['start_date']))->isoFormat('YYYY/MM/DD(ddd) HH:mm'),
                        'end' => (new Carbon($this->schedule['end_date']))->isoFormat('YYYY/MM/DD(ddd) HH:mm'),
                        // 'interview_date' => $interviewStartStr,
                        'interview_venue' => $interviewVenue,
                        'interview_meet_url' => $googleMeetUrlStr,
                    ]
                );

                // ================================================ スケジュールデータ更新 ================================================
                // Googleカレンダーの場合
                if ($this->scheduleType == config('schedule.type.empty.value')) {
                    Schedule::where('id', $this->schedule['id'])
                        ->update([
                            'consumer_id' => $this->consumerId,
                            'title' => '面接確定',
                            'type' => 1,
                            'description' => $scheduleDescription,
                        ]);
                }
                // JobOpカレンダーの場合
                if ($this->scheduleType == config('schedule.type.jobop_available.value')) {
                    if ((int)$this->scheduleBookingCount + 1 >= (int)$this->scheduleCapacity) {
                        $this->scheduleType = 11;
                    }
                    Schedule::where('id', $this->schedule['id'])
                        ->update([
                            // 'consumer_id' => $this->consumerId,
                            'type' => $this->scheduleType,
                            'booking_count' => ++$this->scheduleBookingCount,
                        ]);
                }
                ConsumerSchedule::create([
                    'consumer_id' => $this->consumerId,
                    'schedule_id' => $this->scheduleDecideId,
                ]);

                // ================================================ 合格通知 ================================================
                $passMessage = ShapeTextService::makeSendText(
                    'pass',
                    $worksheetStatusUrl,
                    ['interview_date' => $interviewStartStr,]
                );
                $passMessageMail = ShapeTextService::makeSendText(
                    'pass_mail',
                    $worksheetStatusUrl,
                    [
                        'name' => $consumerName,
                        'interview_date' => $interviewStartStr,
                        'interview_type' => $this->scheduleInterviewVenue,
                        'interview_venue_address' => $this->venueAddress,
                    ]
                );
                $remindPassTypes = [
                    'sms' => $passMessage,
                    'mail' => $passMessageMail,
                ];
                // 現在時間が9:00～20:00であれば3分後、そうでなければ翌日9:00に送信
                if (9 <= Carbon::now()->hour && Carbon::now()->hour < 20) {
                    $send_time = Carbon::now()->addMinutes(3);
                } elseif (Carbon::now()->hour < 9) {
                    $send_time = Carbon::today()->addHours(9);
                } else {
                    $send_time = Carbon::tomorrow()->addHours(9);
                }
                foreach ($remindPassTypes as $remindType => $passMessage) {
                    Remind::create([
                        'consumer_id' => $this->consumerId,
                        'message_type' => $remindType,
                        'next_status' => 'pass' . ($remindType == 'mail' ? '_mail' : ''),
                        'send_text' => $passMessage,
                        'send_time'   => $send_time,
                    ]);
                }
                // ================================================ リマインド ================================================
                // 前日リマインド1
                $theDayBeforeMessage = ShapeTextService::makeSendText(
                    'the_day_before_1',
                    $worksheetStatusUrl,
                    []
                );

                // 前日リマインド1メール
                /** @todo **/
                $theDayBeforeMessageMail = ShapeTextService::makeSendText(
                    'the_day_before_1_mail',
                    $worksheetStatusUrl,
                    [
                        'name' => $consumerName,
                        'interview_date' => $interviewStartStr,
                        'interview_type' => $this->scheduleInterviewVenue,
                        'interview_venue_address' => $this->venueAddress,
                    ]
                );
                $theDayBeforeRemindDate = (clone $interviewStartTime)->subDay(1);
                $remindTypes = [
                    'sms' => $theDayBeforeMessage,
                    'mail' => $theDayBeforeMessageMail,
                ];
                foreach ($remindTypes as $remindType => $message) {
                    Remind::create([
                        'consumer_id' => $this->consumerId,
                        'message_type' => $remindType,
                        'next_status' => Remind::NEXT_STATUS_THE_DAY_BEFORE1 . ($remindType == 'mail' ? '_mail' : ''),
                        'send_text' => $message,
                        'send_time' => $theDayBeforeRemindDate,
                    ]);
                }

                // ================================================ Lambda起動 ================================================
                // test
                // $customerMail = 'akiyama-d@dym.jp';
                $customerMail = Company::find(1)->mail;
                // \Log::debug('先方へメール!!!!!');
                \Log::debug('先方へメール!!!!! : ' . $customerMail);
                LambdaService::apiRequest('common-mail', [
                    'to_address'   => [$customerMail],
                    'cc_address'   => config('aws.lambda.interview.cc_address', []),
                    'bcc_address'  => config('aws.lambda.interview.bcc_address', ['akiyama-d@dym.jp']),
                    'from_address' => config('aws.lambda.interview.from_address'),
                    'subject'      => config('aws.lambda.interview.subject'),
                    'message'      => nl2br($scheduleDescription),
                ]);

                \Log::debug('面接確定の起動');
                // LambdaService::apiRequest('nx-careerroad-status-switcher', [
                //     'status' => [
                //         [
                //             "applicant_id" => $this->atsId,
                //             "next_status" => "面接設定済",
                //             // 'store_id' => '',
                //             // 'decide_date' => '',
                //             // 'desired_slots' => [],
                //         ]
                //     ]
                // ]);

                // event_idがない（Googleカレンダー）はスキップ
                if (!empty($this->schedule['event_id'])) {
                    \Log::info('event_id :' .  $this->schedule['event_id']);
                    LambdaService::apiRequest('nx-careerroad-set-interview-date', [
                        'status' => [
                            [
                                "applicant_id" => $this->atsId,
                                "event_id" => $this->schedule['event_id'],
                                'start' => $this->schedule['start_date'],
                                'end' => $this->schedule['end_date'],
                            ]
                        ]
                    ]);
                }

                // ================================================ スプレッドの転記処理 ================================================
                InterviewInfoForSpreadsheet::create([
                    'consumer_id' => $this->consumerId,
                    'schedule_id' => $this->schedule['id'],
                    'decide_date' => $this->schedule['start_date'] . '~' . $this->schedule['end_date'],
                ]);
                break;
            default:
                break;
        }


        return response()->json([], 200);
    }

    /**
     * 年齢計算
     *
     * @param string $birthdate
     * @return string
     */
    public function calcAge($birthdate)
    {
        $age = "";
        if (!empty($birthdate)) {
            if (
                strpos($birthdate, '年') !== false
                || strpos($birthdate, '月') !== false
                || strpos($birthdate, '日') !== false
            ) {
                preg_match("/([0-9]*)年([0-9]*)月([0-9]*)日/", $birthdate, $dateData);
                $orgBirthday = sprintf("%04.4d%02.2d%02.2d", $dateData[1], $dateData[2], $dateData[3]);
            } elseif (strpos($birthdate, '-') !== false) {
                $dateData = explode('-', $birthdate);
                $orgBirthday = sprintf("%04.4d%02.2d%02.2d", $dateData[0], $dateData[1], $dateData[2]);
            } elseif (strpos($birthdate, '/') !== false) {
                $dateData = explode('/', $birthdate);
                $orgBirthday = sprintf("%04.4d%02.2d%02.2d", $dateData[0], $dateData[1], $dateData[2]);
            } else {
                $orgBirthday = $birthdate;
            }
            $now = date("Ymd");
            $age = strval(floor(($now - $orgBirthday) / 10000));
        }
        return $age;
    }
}
