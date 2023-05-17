<?php

namespace App\Logging;

use Monolog\Logger;
use App\Logging\ChatworkLogAlertHandler;

class ChatworkLogAlert
{
    /**
     * @param  array $config
     *
     * @return \Monolog\Logger
     */
    public function __invoke(array $config)
    {
        $handler = new ChatworkLogAlertHandler(
            $config['token'],
            $config['room'],
            $config['level']
        );

        return new Logger('chatwork', [$handler]);
    }
}
