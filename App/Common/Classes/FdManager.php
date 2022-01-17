<?php


namespace App\Common\Classes;

use EasySwoole\Component\Singleton;
use Swoole\Table;

/**
 * uid与fd，SwooleTable存储
 * Class FdManager
 * @package App\Common\Classes
 */
class FdManager
{
    use Singleton;

    /**
     * key: uid, column: fd
     * @var Table
     */
    protected $uidFd = null;

    /**
     * key: fd, column: uid
     * @var Table
     */
    protected $fdUid = null;

    /**
     * @param int $size SwooleTable行数，即支持的同时在线最大连接数
     */
    public function register($size = 2048)
    {
        $this->fdUid = new Table($size);
        $this->fdUid->column('uid', Table::TYPE_STRING, 128); // 玩家账号最长128
        $this->fdUid->create();

        $this->uidFd = new Table($size);
        $this->uidFd->column('fd', Table::TYPE_INT);
        $this->uidFd->create();
    }

    public function setUidFd($uid, $fd)
    {
        $uid = strval($uid);
        $this->uidFd->set($uid, ['fd' => $fd]);
    }

    public function setFdUid($fd, $uid)
    {
        $fd = strval($fd);
        $this->fdUid->set($fd, ['uid' => $uid]);
    }

    public function getFdByUid($uid)
    {
        $uid = strval($uid);
        return $this->uidFd->exist($uid) ? $this->uidFd->get($uid, 'fd') : false;
    }

    public function delFdByUid($uid)
    {
        $uid = strval($uid);
        return $this->uidFd->exist($uid) ? $this->uidFd->del($uid) : false;
    }

    public function getUidByFd($fd)
    {
        $fd = strval($fd);
        return $this->fdUid->exist($fd) ? $this->fdUid->get($fd, 'uid') : false;
    }

    public function delUidByFd($fd)
    {
        $fd = strval($fd);
        return $this->fdUid->exist($fd) ? $this->fdUid->del($fd) : false;
    }
}
