<?php

namespace App\Logging;

use Illuminate\Log\Logger;

/**
 * アクセスログ用フォーマッターの設定処理。
 */
class AccessLogFormatterTapper
{
    /**
     * 渡されたロガーインスタンスにアクセスログ用フォーマッターを設定する。
     * @param Logger $logger ロガー。
     */
    public function __invoke(Logger $logger) : void
    {
        $formatter = new AccessLogFormatter();
        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter($formatter);
        }
    }
}
