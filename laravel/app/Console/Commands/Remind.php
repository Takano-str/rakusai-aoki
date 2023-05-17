<?php

namespace App\Console\Commands;

// use App\Models\ConsumerDetail;
use App\Models\Remind as ModelRemind;
use App\Services\Aws\AwsSmsService;
use App\Services\Aws\LambdaService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
// use App\Services\Aws\AwsMailService;
use App\Services\Util\FomatterService;
use App\Models\Message;
use Log;

class Remind extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:remind';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'リマインドを送信する';

    private $GetConsumerService;
    private $ReadRemindService;
    private $SendSmsByTel;



    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        \Log::notice('Remind開始');

        // リマインドが必要なものを抽出する
        $nowStamp = Carbon::now('Asia/Tokyo');
        $nowCarbon = $nowStamp->format('Y-m-d H:i');
        $this->info($nowCarbon);
        $reminds = ModelRemind::with('consumerDetail')
            ->where('send_time', 'like binary', "%$nowCarbon%")
            ->get()
            ->toArray();

        if (empty($reminds)) {
            \Log::notice('送信対象のリマインドがありませんでした。');
            return;
        }
        \Log::notice('送信対象のリマインドがあります。');
        foreach ($reminds as $remind) {
            \Log::info($remind);

            // リマインダー送信先の応募者情報を取得
            $consumerId = $remind['consumer_id'];
            $sendText = $remind['send_text'];
            $messageType = $remind['message_type'];
            $sendMessage = $remind['send_text'];
            $remindStatus   = $remind['next_status'];

            // デフォルトの送信先の電話番号、メールを確認
            $consumerId = $remind['consumer_detail']['consumer_id'] ?? '';
            $consumerTel = $remind['consumer_detail']['tel'] ?? '';
            $consumerMail = $remind['consumer_detail']['mail'] ?? '';

            // リマインダーのSMSを送信
            $telValid = FomatterService::validateTel($consumerTel);
            if ($messageType == 'sms' && $telValid) {
                $sms_result = AwsSmsService::sendAwsSms($sendText, $consumerTel);
                $errorMsg = $sms_result->FunctionError ?? "";
            }
            if ($messageType == 'mail') {
                if (empty($consumerMail)) {
                    \Log::info('メールアドレスも見つからなかったため終了');
                    continue;
                }

                // 応募者へメール送信
                //$fromMail = config('app.company_mail');
                $fromMail = 'nx-careerroad@rakusai.sendae.me';

                // 題名を取得
                $subject = "";
                if ($remindStatus == 'pass_mail') {
                    $subject = "NXキャリアロード 面接日のご連絡";
                } elseif ($remindStatus == 'the_day_before_1_mail') {
                    $subject = "NXキャリアロード 面接日のリマインド";
                }

                // $consumerMailParams = [
                //     "subject" => $subject,
                //     "from_address" => $fromMail,
                //     "message" => nl2br($sendMessage),
                //     "to_address" => [$consumerMail],
                //     "cc_address" => [],
                // ];

                // メール送信しなくていい場合もあるのでフラグ用意
                $mailSendFlag = true;
                // if ($remindStatus == ModelRemind::NEXT_STATUS_SECOND) {
                //     \Log::info('メールの時は、初回の２通目リマインドを送信しない');
                //     $mailSendFlag = false;
                // }
                if ($mailSendFlag) {
                    LambdaService::apiRequest('common-mail', [
                        "to_address" => [$consumerMail],
                        'cc_address' => config('aws.lambda.interview.cc_address', []),
                        'bcc_address' => config('aws.lambda.interview.bcc_address', ['akiyama-d@dym.jp']),
                        "from_address" => $fromMail,
                        "subject" => $subject,
                        "message" => nl2br($sendMessage),
                    ]);
                }
            }
            Message::create([
                'consumer_id' => $consumerId,
                // 'company_id' => 1,
                'message' => $sendMessage,
                'send_status' => '0',
            ]);
        }
    }
}
