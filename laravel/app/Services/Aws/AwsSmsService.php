<?php

namespace App\Services\Aws;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;
use App\Services\Aws\LambdaService;
use Log;

class AwsSmsService
{
    /**
     * @var string
     */
    private $spreadId;
    private $range;
    private $sheets;

    public function __construct() {
    }

    /**
     * twilioのAPIを使ったsms送信
     *
     * @param string $message
     * @param string $toTel
     * @param string $fromName
     *
     * @return object $lambdaResponse
     */
    public static function sendAwsSms($message, $toTel)
    {
        $convertTel = self::convertToSmsNumber($toTel);
        $response = [
            'isSuccess' => 'send_ok',
            'twilio_sid' => '-',
        ];
        Log::info($message);

        if (\App::environment() == 'production') {
            $requestInfo = [
                "tel" => $convertTel,
                "password" => config('app.sms_password'),
                "companyName" => config('app.sms_company_name'),
                "message" => $message                    
            ];
            $lambdaResponse = LambdaService::apiRequest(
                'common-sms',
                $requestInfo
            );
        } else {
            $requestInfo = [
                "tel" => $convertTel,
                "password" => config('app.sms_password'),
                "companyName" => config('app.sms_company_name'),
                "message" => $message                    
            ];
            $lambdaResponse = LambdaService::apiRequest(
                'common-sms',
                $requestInfo
            );
        }
        return $lambdaResponse;
    }

    /**
     * E.164規格の電話番号を返す
     * 
     * @param string $tel
     * 
     * @return string $tel
     */
    public static function convertToSmsNumber($tel)
    {
        if (preg_match('/^\+\d{1,15}$/', $tel)) {
            return $tel;
        }
        $tel = mb_convert_kana($tel, 'a');
        $tel = str_replace('-', '', $tel);
        $tel = preg_replace('/^0(\d)/', '+81$1', $tel);
        if (!preg_match('/^\+\d{1,15}$/', $tel)) {
            return '';
        }
        return $tel;
    }

}