<?php

namespace App\Services\Util;

class ShapeTextService
{
    public function __construct() {
    }

    /**
     * 送信するメッセージの取得と置換を行う
     * 
     * @param string $consumerStatus
     * @param string $shortUrl
     * 
     * @return string $sendText
     */
    public static function makeSendText($consumerStatus, $shortUrl, $params)
    {
        $configSearchKey = 'sendMessage.' . $consumerStatus;
        $sendText = config($configSearchKey);
        $sendText = str_replace('{{url}}', $shortUrl, $sendText);

        foreach ($params as $key => $val) {
            $sendText = str_replace('{{' . $key . '}}', $val, $sendText);
        }
        return $sendText;
    }
}