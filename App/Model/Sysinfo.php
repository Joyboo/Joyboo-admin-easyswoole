<?php


namespace App\Model;
use \EasySwoole\EasySwoole\Config;


class Sysinfo extends Base
{
    const TYPE_NUMBER = 0;
    const TYPE_STRING = 1;
    const TYPE_ARRAY = 2;

    public function getSysinfo()
    {
        $all = $this->where('status', 1)->all();
        $sysinfo = [];
        foreach ($all as $item)
        {
            switch ($item['type'])
            {
                case self::TYPE_NUMBER:
                    $sysinfo[$item['varname']] = intval($item['value']);
                    break;
                case self::TYPE_STRING:
                    $sysinfo[$item['varname']] = strval($item['value']);
                    break;
                case self::TYPE_ARRAY:
                    $return = $this->toArraybyEval($item['value']);
                    if (is_array($return)) {
                        $sysinfo[$item['varname']] = $return;
                    }
                    break;
                default:
                    break;
            }
        }
        return $sysinfo;
    }

    public function toArraybyEval($value, $encode = false)
    {
        try {
            $return = eval('return ' . $value . ';');
            if (!is_array($return))
            {
                return false;
            }

            return $encode ? json_encode($return) : $return;
        }
        catch (\Exception | \Throwable $e)
        {
            return false;
        }
    }
}
