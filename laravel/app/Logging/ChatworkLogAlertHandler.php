<?php

namespace App\Logging;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use GuzzleHttp\Client;
/**
 * ChatworkHandlerService class
 */
class ChatworkLogAlertHandler extends AbstractProcessingHandler
{
    protected $token;
    protected $room;

    /**
     * ChatWorkHandler constructor.
     *
     * @param string $token
     * @param string $room
     * @param int    $level
     * @param bool   $bubble
     */
    public function __construct(
        string $token,
        string $room,
        $level = Logger::DEBUG,
        bool $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->token = $token;
        $this->room = $room;
    }

    /**
     * {@inheritdoc}
     *
     * @param array $record
     *
     * @return void
     */
    protected function write(array $record):void
    // protected function write(array $record)
    {
        $client = new Client();
        $url = "https://api.chatwork.com/v2/rooms/{$this->room}/messages";
        $env = config('app.env');
        $body = sprintf("[toall]\n$env NXキャリアロード\n[info]%s[/info]", $record['formatted']);
        $res = $client->post($url, [
            'headers'     => ['X-ChatWorkToken' => $this->token],
            'form_params' => ['body' => $body],
        ]);
    }
}
