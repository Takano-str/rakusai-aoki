<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Consumer;
use App\Models\ConsumerDetail;
use App\Models\ConsumerStatusHistory;
use App\Models\Message;
use App\Models\Store;
use App\Models\Worksheet;
// use App\Models\ConsumerRejection;
// use App\Models\MasterConsumerStatus;
// use App\Models\UniqueConsumer;

use App\Services\Chatwork\ChatworkService;
use App\Services\Aws\AwsSmsService;
use App\Services\Aws\AwsMailService;
use App\Services\Aws\LambdaService;
use App\Services\Util\FomatterService;
use App\Services\Util\CreateUrlService;
use App\Services\Util\ShapeTextService;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class AddConsumerController extends Controller
{
    // レスポンスで返却する対応ステータス
    const RESPONSE_TYPE_CREATE_NEW       = 0; // 応募者データ新規登録
    const RESPONSE_TYPE_CREATE_FAILED    = 1; // 応募者データ新規登録失敗
    const RESPONSE_TYPE_CREATED          = 2; // 応募者データ登録済み
    const RESPONSE_TYPE_REJECTION        = 3; // 応募者拒否登録済み
    const RESPONSE_TYPE_SMS_FAILED       = 4; // 応募者データ新規登録(SMS送信は失敗)
    const RESPONSE_TYPE_MISMATCH_ADDRESS = 5; // 住所が取り込み対象ではない
    const RESPONSE_TYPE_MISMATCH_TEL     = 6; // 電話番号が取り込み対象ではない

    // レスポンスで返却するHTTPステータスコード
    const HTTP_STATUS_CODE_SUCCESS = 200;
    const HTTP_STATUS_CODE_ERROR   = 400;

    private $response_code = self::HTTP_STATUS_CODE_SUCCESS;

    // レスポンスで返却するJSON
    private $responses = [
        self::RESPONSE_TYPE_CREATE_NEW       => [],
        self::RESPONSE_TYPE_CREATE_FAILED    => [],
        self::RESPONSE_TYPE_CREATED          => [],
        self::RESPONSE_TYPE_REJECTION        => [],
        self::RESPONSE_TYPE_SMS_FAILED       => [],
        self::RESPONSE_TYPE_MISMATCH_ADDRESS => [],
        self::RESPONSE_TYPE_MISMATCH_TEL     => [],
    ];

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Log::info("\n[start]:api/add-consumer");
        Log::notice($request->all());

        try {
            // APIトークンチェック
            if ($request->get('api_token') !== config('app.api_token')) {
                throw new \Exception('APIトークンが不正です。');
            }

            // 管理画面からの登録の場合はユーザID取得
            // $userId = $request->get('user_id', '');

            // リクエストの応募者データごとに以下を処理
            foreach ($request->get('applicants', []) as &$applicant) {
                $applicant['uniqueId'] = $applicant['uniqueId'] ?? uniqid(rand() . '_');
                $applicantStoreId = $applicant['ats_store_id'] ?? uniqid(rand() . '_');
                $applicantTel = $applicant['tel'];
                $applicantAtsId = $applicant['ats_id'] ?? '';
                $applicantName = $applicant['name'] ?? '';
                $applicantKana = $applicant['kana'] ?? '';
                $applicantMail = $applicant['email'] ?? '';
                $applicantAddress = $applicant['address'] ?? '';
                $applicantGender = $applicant['gender'] ?? '';

                $isExistStore = Store::where('id', $applicantStoreId)
                    ->exists();
                if (!$isExistStore) {
                    \Log::emergency('店舗の紐付けが存在しませんでした！');
                    continue;
                }


                // 電話番号を整形
                $applicantTel = mb_convert_kana($applicantTel, 'a');
                if (strpos($applicantTel, "+81") !== false) {
                    $applicantTel = str_replace("+81", "0", $applicantTel);
                    $applicantTel = str_replace(array(" ", "　"), "", $applicantTel);
                }

                // 応募拒否済みの電話番号は登録処理をしない
                // $blackListFlag = false;

                // 電話番号が完全一致 & 現在日以内で作成されたデータ
                $consumers_applied = ConsumerDetail::where('tel', '=', $applicantTel ?? '')
                    ->where('created_at', '>', Carbon::now()->subHours(72))
                    ->exists();

                // 現在日内に応募があった場合は処理しない
                if (!empty($consumers_applied)) {
                    /** @todo Indeed対応 あとで直さないといけない **/
                    if ($applicantTel == 'null') {
                        $this->output("電話番号がなかった応募者", $applicant);
                    } else {
                        $this->output("3日内で同じ電話番号からの応募があったため以下の応募者を取り込みませんでした。", $applicant);
                        $this->responses[self::RESPONSE_TYPE_CREATED][] = $applicant;
                        continue;
                    }
                }

                // 応募者データ登録
                $consumer_id = Consumer::create([
                    'name' => '',
                    'ats_id' => $applicantAtsId,
                    'company_id' => 1,
                    'store_id' => $applicantStoreId,
                ])->id;
                ConsumerDetail::create([
                    'consumer_id' => $consumer_id,
                    'name' => $applicantName,
                    'kana' => $applicantKana,
                    'tel' => $applicantTel,
                    'mail' => $applicantMail,
                    'address' => $applicantAddress,
                    'gender' => $applicantGender,
                ]);
                ConsumerStatusHistory::create([
                    'consumer_id' => $consumer_id,
                    'history_number' => 1,
                    'status_code' => 2,
                    'changer_id' => 1,
                ]);

                // アンケートのURLの発行
                $worksheet_url = CreateUrlService::makeWorksheetUrl(
                    'worksheet',
                    [
                        'csid' => $consumer_id,
                        'cskey' => Crypt::encrypt($consumer_id),
                    ]
                );

                Worksheet::create([
                    'store_id' => $applicantStoreId,
                    'consumer_id' => $consumer_id,
                    'worksheet_url' => $worksheet_url,
                ]);

                ChatworkService::send(
                    "[info]応募者登録通知[/info] \n"
                        . "実行環境 : " . \App::environment() . "\n"
                        . "応募者ID : {$consumer_id} \n"
                );

                \Log::debug('test!!!!!!!!');

                if (empty($request->has('sms'))) {
                    // 管理画面経由でSMSを送信しないを選択している場合は以降をスキップ
                    $this->output("以下の応募者を正常に取り込みました。 ", $applicant);
                    // $this->responses[self::RESPONSE_TYPE_CREATE_NEW][] = $applicant;
                    // continue;
                }

                // 応募者へSMS送信
                try {
                    // $message = $this->MakeTextService->makeSendText('new', $worksheet_url);
                    $message = ShapeTextService::makeSendText(
                        'new',
                        $worksheet_url,
                        []
                    );
                    // $secondMessage = $this->MakeTextService->makeSendText('second', '');
                    \Log::debug('応募者へSMS送信!!!!!!!!');
                    \Log::notice($applicant);
                    $sms_result = AwsSmsService::sendAwsSms($message, $applicant['tel']);
                    \Log::notice($sms_result);

                    if ($sms_result == 'error') {
                        throw new \Twilio\Exceptions\TwilioException("SMS送信失敗");
                    }

                    Log::notice("以下のSMSを送信しました。\n" . $message);
                    // $this->output("以下の応募者をとりこみました。", $applicant);
                    // $this->responses[self::RESPONSE_TYPE_CREATE_NEW][] = $applicant;

                    // 暫定対応：メールも送信
                    try {
                        $isMailSend = true;
                        $telValid = FomatterService::validateTel($applicant['tel']);
                        $errorMsg = $sms_result->FunctionError ?? "";
                        if (!empty($errorMsg) || !$telValid) {
                            // $isMailSend = false;
                        }

                        // NXはメールは必ず送る？
                        if ($isMailSend) {
                            // $message  = $this->MakeTextService->makeAddConsumerMailDescription(true, $worksheet_url);
                            // $mailMessage = \str_replace('SMS', 'メール', $message);
                            $mailMessage = ShapeTextService::makeSendText(
                                'new_mail',
                                $worksheet_url,
                                [
                                    'name' => $applicantName,
                                ]
                            );
                            // $parameters = [
                            //     'to_address'   => [$applicantMail],
                            //     'cc_address'   => [],
                            //     'bcc_address'  => [],
                            //     'from_address' => env('COMPANY_MAIL'),
                            //     'subject'      => '',
                            //     'message'      => nl2br($mailMessage),
                            // ];
                            // test
                            // AwsMailService::sendMail($parameters);
                            LambdaService::apiRequest('common-mail', [
                                'to_address'   => [$applicantMail],
                                'cc_address'   => config('aws.lambda.interview.cc_address', []),
                                'bcc_address'  => config('aws.lambda.interview.bcc_address', ['akiyama-d@dym.jp']),
                                'from_address' => config('aws.lambda.interview.from_address'),
                                'subject'      => 'NXキャリアロード 事前アンケートのお願い',
                                'message'      => nl2br($mailMessage),
                            ]);
                            Log::notice("以下のメールを送信しました。\n" . $mailMessage);
                        }
                    } catch (\Throwable $th) {
                        \Log::error($th);
                    }
                } catch (\Throwable $th) {
                    \Log::error($th);
                    
                    // 応募者へのSMS送信に失敗した場合はメール送信
                    // $message  = $this->MakeTextService->makeAddConsumerMailDescription(true, $worksheet_url);
                    // $this->LambdaMailService->sendMailByAddConsumer($applicant, $message);
                    // Log::notice("以下のメールを送信しました。\n" . $message);
                    // $this->output("以下の応募者をとりこみましたが、SMS送信に失敗したためメールを送信しました。", $applicant);
                    // $this->responses[self::RESPONSE_TYPE_SMS_FAILED][] = $applicant;
                }

                // 送信メッセージ保存
                foreach ([$message] as $msg) {
                    Message::create([
                        'consumer_id'   => $consumer_id,
                        // 'company_id'    => 1,
                        'send_status'   => '0',
                        'message'       => $msg,
                    ]);
                }

                /** @todo ブラックリストフラグ**/
                // if ($blackListFlag) {
                //     Consumer::where('id', $consumer_id)
                //         ->update([
                //             'black_list_flag' => $blackListFlag,
                //         ]);
                //     try {
                //         // 拒否登録の場合はステータス更新
                //         $consumerStatusHistory = ConsumerStatusHistory::where('consumer_id', '=', $consumer_id)
                //             ->orderby('history_number', 'DESC')
                //             ->first();
                //         ConsumerStatusHistory::create([
                //             'consumer_id'    => $consumer_id,
                //             'history_number' => ++$consumerStatusHistory->history_number,
                //             'status_code'    => MasterConsumerStatus::CONSUMER_STATUS_BLACK_LIST,
                //         ]);
                //     } catch (\Throwable $th) {
                //         //throw $th;
                //     }
                // }

            }
        } catch (\Exception $e) {
            // report($e);
            // $this->response_code = self::HTTP_STATUS_CODE_ERROR;
            \Log::error($e);
        }

        Log::info("\n[end]:api/add-consumer");
        return response()->json($this->responses, $this->response_code);
    }

    /**
     * 応募拒否登録がされている電話番号か確認する
     * @param  String $tel
     * @return Bool
     */
    private function checkRejection($tel)
    {
        $check = ConsumerRejection::where('tel', $tel)->exists();
        return $check;
    }

    /**
     * ログ出力を行う
     * @param  String $message
     * @param  Array  $applicant 応募者のリクエストデータ
     */
    private function output($message, $applicant = [])
    {
        Log::notice($message);
    }
}
