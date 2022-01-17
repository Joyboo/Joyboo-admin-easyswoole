<?php

namespace App\Model;

use App\Task\VersionUpdate;
use EasySwoole\EasySwoole\Task\TaskManager;


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

    // todo 更新版本暂时这么做，后面要优化
    protected static function onAfterUpdate(Base $model, $res)
    {
       $varname = $model->getAttr('varname');
        foreach (['version_force', 'version_later'] as $col)
        {
            if ($varname === $col)
            {
                $force = $col === 'version_force' ? 1: 0;
                TaskManager::getInstance()->async(new VersionUpdate(['force' => $force]));
            }
        }
    }
}
