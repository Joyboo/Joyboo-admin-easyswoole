<?php


namespace App\Common\Handler;

use EasySwoole\Log\LoggerInterface;

class Log implements LoggerInterface
{
    private $logDir;

    function __construct(string $logDir = null)
    {
        $this->logDir = $logDir ? : '';
    }

    function log(?string $msg, int $logLevel = self::LOG_LEVEL_INFO, string $category = 'debug'): string
    {
        if (empty($this->logDir))
        {
            $this->logDir = config('LOG.dir');
        }
        // 按月分目录
        $dir = $this->logDir . '/' . date('Ym');
        is_dir($dir) or @ mkdir($dir);
        // 按日分文件
        $filePath = $dir . '/' . date('d') . "_{$category}.log";

        $date = date('Y-m-d H:i:s');
        $levelStr = $this->levelMap($logLevel);
        $str = "[{$date}][{$levelStr}] : [{$msg}]\n";

        file_put_contents($filePath, "{$str}", FILE_APPEND | LOCK_EX);
        return $str;
    }

    function console(?string $msg, int $logLevel = self::LOG_LEVEL_INFO, string $category = 'console')
    {
        $date = date('Y-m-d H:i:s');
        $levelStr = $this->levelMap($logLevel);
        $temp = "[{$date}][{$category}][{$levelStr}]:[{$msg}]\n";
        fwrite(STDOUT, $temp);
    }

    private function levelMap(int $level)
    {
        switch ($level) {
            case self::LOG_LEVEL_INFO:
                return 'info';
            case self::LOG_LEVEL_NOTICE:
                return 'notice';
            case self::LOG_LEVEL_WARNING:
                return 'warning';
            case self::LOG_LEVEL_ERROR:
                return 'error';
            default:
                return 'unknown';
        }
    }
}
