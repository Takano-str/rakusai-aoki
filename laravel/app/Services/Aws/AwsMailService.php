<?php

namespace App\Services\Aws;

use App\Services\LambdaService;
use Log;

class AwsMailService
{
    public static $lambdaFunctionName = 'common-mail';
    public static $lambdaParameters = [
        'to_address',
        'cc_address',
        'bcc_address',
        'from_address',
        'subject',
        'message',
    ];


    public function __construct()
    {
    }

    /**
     * 新規応募者へアンケートURL誘導用のメールを送信する
     * @param  Array  $consumer
     * @param  String $message
     */
    public static function sendMailByAddConsumer($consumer, $message)
    {
        // $recruitAdminMail = Company::find(1)->recruit_admin_mail ? [Company::find(1)->recruit_admin_mail] : [];
        // self::sendMail([
        //     'to_address'   => [$consumer['mail']],
        //     'cc_address'   => config('aws.lambda.' . __FUNCTION__ . '.cc_address', []),
        //     'bcc_address'  => $recruitAdminMail,
        //     'from_address' => config('aws.lambda.' . __FUNCTION__ . '.from_address'),
        //     'subject'      => config('aws.lambda.' . __FUNCTION__ . '.subject'),
        //     'message'      => nl2br($message),
        // ]);
    }

    /**
     * メールを送信する
     * @param  Array $parameters
     */
    public static function sendMail($parameters)
    {
        if (array_keys($parameters) != self::$lambdaParameters) {
            Log::error("The arguments passed to the AWS Lambda common-mail function do not match.");
            Log::error($parameters);
        }

        $functionName = self::$lambdaFunctionName;
        if (\App::environment() != 'production') {
            $functionName = self::$lambdaFunctionName .'-test';
        }
        LambdaService::apiRequest($functionName, $parameters);
    }
}
