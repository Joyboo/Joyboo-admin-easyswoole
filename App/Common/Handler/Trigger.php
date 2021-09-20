<?php


namespace App\Common\Handler;

use EasySwoole\EasySwoole\Logger;
use EasySwoole\Trigger\Location;
use EasySwoole\Trigger\TriggerInterface;

class Trigger implements TriggerInterface
{
    public function error($msg, int $errorCode = E_USER_ERROR, Location $location = null)
    {
        if (in_array($errorCode, [E_NOTICE]))
        {
            return;
        }

        if($location == null){
            $location = new Location();
            $debugTrace = debug_backtrace();
            $caller = array_shift($debugTrace);
            $location->setLine($caller['line']);
            $location->setFile($caller['file']);
        }
        $exp = [
            'message' => $msg,
            'file' => $location->getFile(),
            'line' => $location->getLine()
        ];
        $this->doError(__FUNCTION__, $exp);
    }

    public function throwable(\Throwable $throwable)
    {
        $exp = [
            'message' => $throwable->getMessage(),
            'file' => $throwable->getFile(),
            'line' => $throwable->getLine(),
            'trace' => $throwable->getTrace()
        ];
        $this->doError(__FUNCTION__, $exp);
        throw $throwable;
    }

    protected function doError($trigger, $exp)
    {
        trace($exp, $trigger, $trigger);
    }
}
