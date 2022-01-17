<?php


namespace App\Model;
use \EasySwoole\EasySwoole\Config;


class Sysinfo extends Base
{
    const TYPE_NUMBER = 0;
    const TYPE_STRING = 1;
    const TYPE_ARRAY = 2;

    protected function setValueAttr($value, $all)
    {
        return $this->setValue($value, $all['type'], false);
    }

    protected function getValueAttr($value, $all)
    {
        return $this->setValue($value, $all['type'], true);
    }

    protected function setValue($value, $type, $decode = true)
    {
        if ($type == self::TYPE_NUMBER) {
            $value = intval($value);
        }
        else if ($type == self::TYPE_STRING)
        {
            $value = strval($value);
        }
        else {
            if ($decode) {
                $json = json_decode($value, true);
            }
            elseif (is_array($value)) {
                $json = json_encode($value, JSON_UNESCAPED_UNICODE);
            }
            $json && $value = $json;
        }
        return $value;
    }

    public function getSysinfo()
    {
        $all = $this->where('status', 1)->all();
        $result = [];
        foreach ($all as $value)
        {
            $result[$value->varname] = $value->value;
        }
        return $result;
    }
}
