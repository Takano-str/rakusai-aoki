<?php

namespace App\Services\Chatwork;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class ChatworkService
{
    const CHATWORK_ROOM_ID = '';
    const CHATWORK_TOKEN   = '';

    /**
     * チャットワークにメッセージを送信する
     * @param  String $message
     */
    public static function send($message)
    {
        $env = App::environment();
        if(strpos($env, 'production') === false) {
            \Log::debug('prtoduction以外は通知を飛ばさない');
            return;
        }

        try {
            $ch = curl_init();
            $options = [
                CURLOPT_URL            => "https://api.chatwork.com/v2/rooms/" . self::CHATWORK_ROOM_ID . "/messages",
                CURLOPT_HTTPHEADER     => array('X-ChatWorkToken: ' . self::CHATWORK_TOKEN),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => http_build_query([
                    'body' => $message
                ], '', '&'),
            ];
            curl_setopt_array($ch, $options);
            $result = curl_exec($ch);
            curl_close($ch);
        } catch (Exception $e) {
            $res = json_decode($result);
            Log::error("Chatworkの通知に失敗しました。\n");
            Log::error($res);
            Log::error($e);
        }
    }
}
