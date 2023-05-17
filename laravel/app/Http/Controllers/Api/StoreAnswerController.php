<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Consumer;
use App\Models\ConsumerStatusHistory;
use App\Models\InterviewInfoForSpreadsheet;
use App\Models\Remind;
use App\Models\Schedule;
use App\Models\Worksheet;
use App\Services\Aws\LambdaService;
use App\Services\Util\CreateUrlService;
use App\Services\Util\ShapeTextService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;


class StoreAnswerController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \Log::info($request);

        $this->consumerId = $request->get('consumerId');
        $answerData = $request->get('answerData', []);
        $howToRespond = $answerData['scheduleType'];
        $desireDate = $answerData['interviewDate'];
        $interviewVenue = $answerData['interviewVenue'] ?? '';

        $consumer = Consumer::where('id', $this->consumerId)
            ->with('consumerDetail')
            ->with('worksheet')
            ->first()
            ->toArray();
        $worksheetId = $consumer['worksheet']['id'];
        $storeId = $consumer['store_id'];
        $atsId = $consumer['ats_id'];

        /** @todo **/ 
        $desireDate = str_replace('/', '-', $desireDate);
        $interviewStartStr = explode("~", $desireDate)[0];
        $interviewEndStr = explode("~", $desireDate)[1];
        \Log::debug($interviewStartStr);
        // $interviewStartTime = '';
        // $interviewStartTime = new Carbon($interviewStartStr);
        $interviewStartTime = (new Carbon($interviewStartStr))->format('Y-m-d H:i');
        $interviewEndTime = (new Carbon($interviewEndStr))->format('Y-m-d H:i');
        
        // 対応「電話・メールで個別に応募者と調整する」選択時はここで終了
        if ($howToRespond == 'self_adjustment') {
            return response()->json([], 200);
        }

        // ショートURL生成
        $worksheetStatusUrl = CreateUrlService::makeWorksheetUrl(
            'worksheetStatus',
            ['csid' => Crypt::encrypt($this->consumerId),]
        );
        Worksheet::where('id', $worksheetId)
            ->update([
                'status_url' => $worksheetStatusUrl,
                'store_answer' => json_encode($answerData, JSON_UNESCAPED_UNICODE),
            ]);

        // 面接日時の確定
        $scheduleDescription = ShapeTextService::makeSendText(
            'scheduleDescription',
            $worksheetStatusUrl,
            [
                'name' => $consumer['consumer_detail']['name'] ?? '',
                'tel' => $consumer['consumer_detail']['tel'] ?? '登録されていません',
                'mail' => $consumer['consumer_detail']['name'] ?? '登録されていません',
                'start' => $interviewStartTime,
                'end' => $interviewEndTime,
                // 'interview_date' => $interviewStartStr,
                'interview_venue' => '登録されていません',
            ]
        );

        $schedule = Schedule::create([
            'consumer_id' => $this->consumerId,
            'event_id' => '',
            'store_id' => $storeId,
            'title' => '面接確定',
            'type' => 1,
            'interview_venue' => '',
            'description' => $scheduleDescription,
            'start_date' => $interviewStartTime,
            'end_date' => $interviewEndTime,
        ]);

        // 合格通知
        $passMessage = ShapeTextService::makeSendText(
            'pass',
            $worksheetStatusUrl,
            ['interview_date' => $interviewStartStr,]
        );
        // 現在時間が9:00～20:00であれば10分後、そうでなければ翌日9:00に送信
        if (9 <= Carbon::now()->hour && Carbon::now()->hour < 20) {
            $send_time = Carbon::now()->addMinutes(10);
        } elseif (Carbon::now()->hour < 9) {
            $send_time = Carbon::today()->addHours(9);
        } else {
            $send_time = Carbon::tomorrow()->addHours(9);
        }
        Remind::create([
            'consumer_id' => $this->consumerId,
            'send_text' => $passMessage,
            'send_time'   => $send_time,
            'next_status' => 'pass',
        ]);
        // 前日リマインド1
        $theDayBeforeMessage = ShapeTextService::makeSendText(
            'the_day_before_1',
            $worksheetStatusUrl,
            ['interview_date' => $interviewStartStr,]
        );
        $theDayBeforeRemindDate = (new Carbon($interviewStartStr))->subDay(1);
        Remind::create([
            'consumer_id' => $this->consumerId,
            'send_text' => $theDayBeforeMessage,
            'next_status' => Remind::NEXT_STATUS_THE_DAY_BEFORE1,
            'send_time' => $theDayBeforeRemindDate,
        ]);

        // 先方へメール
        // test
        $customerMail = Company::find(1)->mail;

        \Log::debug('先方へメール!!!!! : ' . $customerMail);
        LambdaService::apiRequest('common-mail', [
            'to_address'   => [$customerMail],
            'cc_address'   => config('aws.lambda.interview.cc_address', []),
            'bcc_address'  => config('aws.lambda.interview.bcc_address', ['akiyama-d@dym.jp']),
            'from_address' => config('aws.lambda.interview.from_address'),
            'subject'      => config('aws.lambda.interview.subject'),
            'message'      => nl2br($scheduleDescription),
        ]);

        \Log::debug('スイッチャー起動!!!!!');
        LambdaService::apiRequest('nx-careerroad-status-switcher', [
            'status' => [
                [
                    "applicant_id" => $atsId,
                    "next_status" => "面接設定済",
                    // 'store_id' => '',
                    // 'decide_date' => '',
                    // 'desired_slots' => [],
                ]
            ]
        ]);
        InterviewInfoForSpreadsheet::create([
            'consumer_id' => $this->consumerId,
            'schedule_id' => $schedule->id,
            'decide_date' => $schedule->start_date . '~' . $schedule->end_date,
        ]);
    }
}
