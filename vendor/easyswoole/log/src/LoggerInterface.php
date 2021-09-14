<?php


namespace EasySwoole\Log;


interface LoggerInterface
{
    const LOG_LEVEL_DEBUG = 0;
    const LOG_LEVEL_INFO = 1;
    const LOG_LEVEL_NOTICE = 2;
    const LOG_LEVEL_WARNING = 3;
    const LOG_LEVEL_ERROR = 4;

    function log(?string $msg,int $logLevel = self::LOG_LEVEL_DEBUG,string $category = 'debug');
    function console(?string $msg,int $logLevel = self::LOG_LEVEL_DEBUG,string $category = 'debug');
}