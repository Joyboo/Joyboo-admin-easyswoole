<?php

use App\Common\Classes\FdManager;
use EasySwoole\EasySwoole\ServerManager;

// 从配置获取，不依赖sysinfo
function is_super($rid = null)
{
    $super = config('SUPER_ROLE');
    return $super && is_array($super) && in_array($rid, $super);
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
