<?php


namespace App\Common\Classes;

use EasySwoole\Component\TableManager;
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

    protected $uidFdKey = 'tb-uid-fd';

    protected $fdUidKey = 'tb-fd-uid';

    /**
     * @param int $size SwooleTable行数，即支持的同时在线最大连接数
     */
    public function register($size = 2048)
    {
        $config = [
            [
                'name' => $this->fdUidKey,
                'columns' => ['uid' => ['type' => Table::TYPE_STRING, 'size' => 128]],
            ],
            [
                'name' => $this->uidFdKey,
                'columns' => ['fd' => ['type' => Table::TYPE_INT]],
            ],
        ];

        foreach ($config as $value)
        {
            TableManager::getInstance()->add($value['name'], $value['columns'], $value['size'] ?? $size);
        }
    }

    public function getTable($name)
    {
        return TableManager::getInstance()->get($name);
    }

    public function setUidFd($uid, $fd)
    {
        $uid = strval($uid);
        $this->getTable($this->uidFdKey)->set($uid, ['fd' => $fd]);
    }

    public function setFdUid($fd, $uid)
    {
        $fd = strval($fd);
        $this->getTable($this->fdUidKey)->set($fd, ['uid' => $uid]);
    }

    public function getFdByUid($uid)
    {
        $uid = strval($uid);
        $table = $this->getTable($this->uidFdKey);
        return $table && $table->exist($uid) ? $table->get($uid, 'fd') : false;
    }

    public function delFdByUid($uid)
    {
        $uid = strval($uid);
        $table = $this->getTable($this->uidFdKey);
        return $table && $table->exist($uid) ? $table->del($uid) : false;
    }

    public function getUidByFd($fd)
    {
        $fd = strval($fd);
        $table = $this->getTable($this->fdUidKey);
        return $table && $table->exist($fd) ? $table->get($fd, 'uid') : false;
    }

    public function delUidByFd($fd)
    {
        $fd = strval($fd);
        $table = $this->getTable($this->fdUidKey);
        return $table && $table->exist($fd) ? $table->del($fd) : false;
    }
}
