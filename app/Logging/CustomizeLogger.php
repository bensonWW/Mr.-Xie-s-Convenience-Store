<?php

namespace App\Logging;

use Illuminate\Log\Logger;

class CustomizeLogger
{
    /**
     * Customize the given logger instance.
     *
     * @param  \Illuminate\Log\Logger  $logger
     * @return void
     */
    public function __invoke(Logger $logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->pushProcessor(new SanitizeLogProcessor());
        }
    }
}
