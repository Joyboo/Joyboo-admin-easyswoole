<?php

use App\Common\Classes\FdManager;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Spl\SplArray;
use EasySwoole\RedisPool\RedisPool;
use EasySwoole\Redis\Redis;

use App\Model\Sysinfo;

function isSuper($rid = null)
{
    $super = config('SUPER_ROLE');
    return in_array($rid, $super);
}

/**
 * fd是否在线
 * @param $fd
 * @return mixed
 */
function is_online_fd($fd)
{
    return ServerManager::getInstance()->getSwooleServer()->isEstablished($fd);
}

/**
 * uid是否在线
 * @param $uid
 * @return bool|mixed
 */
function is_online_uid($uid)
{
    $fd = FdManager::getInstance()->getFdByUid($uid);
    if (!$fd) {
        return false;
    }
    return is_online_fd($fd);
}

/**
 * 获取系统设置的动态配置
 * @document http://www.easyswoole.com/Components/Spl/splArray.html
 * @param string|true|null $key true-直接返回SplArray对象，非true取值与 SplArray->get 相同
 * @param string|null $default 默认值
 * @return array|SplArray|mixed|null
 */
function sysinfo($key = null, $default = null) {

    /** @var SplArray $Spl */
    $Spl = RedisPool::invoke(function (Redis $redis) {

        $redisKey = Sysinfo::CACHE_KEY;

        $cache = $redis->get($redisKey);
        if ($cache !== false && !is_null($cache))
        {
            $slz = unserialize($cache);
            if ($slz instanceof SplArray)
            {
                return $slz;
            }
        }

        $model = Sysinfo::create();
        $data = $model->where('status', 1)->all();

        $array = [];
        /** @var Sysinfo $item */
        foreach ($data as $item)
        {
            $array[$item->getAttr('varname')] = $item->getAttr('value');
        }

        $Spl = new SplArray($array);
        $redis->set($redisKey, serialize($Spl));
        return $Spl;
    });

    return $key === true ? $Spl : $Spl->get($key, $default);
}
