<?php


namespace App\Common\Classes;

use EasySwoole\Component\Singleton;
use EasySwoole\EasySwoole\ServerManager;
use Swoole\Table;
use EasySwoole\Component\TableManager;

/**
 * uid与fd，SwooleTable存储
 * Class FdManager
 * @package App\Common\Classes
 */
class FdManager
{
    use Singleton;

    const UID_FD_KEY = 'tb-uid-fd';

    const FD_UID_KEY = 'tb-fd-uid';

    protected $fdColumnSize = 300;

    protected $fdColumnName = 'fds';

    /**
     * @param int $size SwooleTable行数，即支持的同时在线最大连接数
     */
    public function register($size = 2048)
    {
        // 以fd为key
        TableManager::getInstance()->add(
            self::FD_UID_KEY,
            [
                'uid' => ['type' => Table::TYPE_INT, 'size' => null],
                'token' => ['type' => Table::TYPE_STRING, 'size' => 1000],
            ],
            $size * 2 // 此表行数应该是下面表的N倍（平均每个用户单开就是1倍，平均每个用户双开就是2倍）
        );

        // 以uid为key
        TableManager::getInstance()->add(
            self::UID_FD_KEY,
            [
                $this->fdColumnName => ['type' => Table::TYPE_STRING, 'size' => $this->fdColumnSize]
            ],
            $size
        );
    }

    // 返回所有table用于外部遍历
    public function getTableAll()
    {
        $tables = [];
        foreach ([self::FD_UID_KEY, self::UID_FD_KEY] as $tbname)
        {
            $tables[$tbname] = $this->getTable($tbname);
        }
        return $tables;
    }

    public function getTable($name)
    {
        return TableManager::getInstance()->get($name);
    }

    /**
     * 为减小hash冲突率，加key前缀
     * @param string $tableName
     * @param $key
     * @return string
     */
    public function getRowKey(string $tableName, $key)
    {
        return $tableName . '-' . $key;
    }

    /************************** 多个fd存储在一个table内 ****************************/
    public function fmtFds(array $fdArray): string
    {
        $str = implode(',', $fdArray);
        $size = strlen($str);

        if ($size > $this->fdColumnSize) {
            // todo 处理超过 fdColumnSize 长度的场景，否则会被截取，会导致程序异常
        }

        return $str;
    }

    public function unfmtFds(string $fdString): array
    {
        return explode(',', $fdString);
    }

    /**
     * @param $uid
     * @param $fd
     * @return array
     */
    public function setRowFd($uid, $fd)
    {
        $rowKey = $this->getRowKey(self::UID_FD_KEY, $uid);
        $table = $this->getTable(self::UID_FD_KEY);
        $row = $table->get($rowKey, $this->fdColumnName);

        $array = [];
        if ($row !== false) {
            $fdArray = $this->unfmtFds($row);
            foreach ($fdArray as $val) {
                if ($this->fdExist($val)) {
                    $array[] = $val;
                }
            }
        }
        $array[] = $fd;
        $table->set($rowKey, [$this->fdColumnName => $this->fmtFds($array)]);
    }

    /**
     * 删除uid的某一个连接
     * @return void
     */
    public function delRowFd($uid, $fd)
    {
        $rowKey = $this->getRowKey(self::UID_FD_KEY, $uid);
        $table = $this->getTable(self::UID_FD_KEY);

        if ($table->exist($rowKey)) {
            $row = $table->get($rowKey, $this->fdColumnName);
            $fdArray = $this->unfmtFds($row);

            $array = [];
            foreach ($fdArray as $val) {
                if ($val != $fd && $this->fdExist($val)) {
                    $array[] = $val;
                }
            }

            if (empty($array)) {
                $table->del($rowKey);
            } else {
                $table->set($rowKey, [$this->fdColumnName => $this->fmtFds($array)]);
            }
        }
    }

    public function setRowUid($fd, $uid, $token)
    {
        $rowKey = $this->getRowKey(self::FD_UID_KEY, $fd);
        $this->getTable(self::FD_UID_KEY)->set($rowKey, ['uid' => $uid, 'token' => $token]);
    }

    public function delRowUid($fd)
    {
        $rowKey = $this->getRowKey(self::FD_UID_KEY, $fd);
        $table = $this->getTable(self::FD_UID_KEY);
        return $table->del($rowKey);
    }

    public function getUidByFd($fd, string $field = null)
    {
        $rowKey = $this->getRowKey(self::FD_UID_KEY, $fd);
        $table = $this->getTable(self::FD_UID_KEY);
        return $table->get($rowKey, $field);
    }

    /**
     * 为uid内的所有链接执行function
     * @param $uid
     * @param callable $call function ($fd) {}
     * @return false|void
     */
    public function uidForeach($uid, callable $call)
    {
        $rowKey = $this->getRowKey(self::UID_FD_KEY, $uid);
        $table = $this->getTable(self::UID_FD_KEY);
        if ( ! $table->exist($rowKey)) {
            return false;
        }
        $Server = ServerManager::getInstance()->getSwooleServer();
        $row = $this->unfmtFds($table->get($rowKey, $this->fdColumnName));

        foreach ($row as $colKey => $fd) {
            if ($Server->isEstablished($fd)) {
                $call($fd, $Server);
            }
        }
    }

    /**
     * 为所有连接执行function
     * @param callable $call
     * @return void
     */
    public function allForeach(callable $call)
    {
        $table = $this->getTable(self::UID_FD_KEY);
        $Server = ServerManager::getInstance()->getSwooleServer();
        foreach ($table as $rows)
        {
            $fds = $this->unfmtFds($rows[$this->fdColumnName]);
            foreach ($fds as $fd)
            {
                if ($Server->isEstablished($fd)) {
                    $call($fd, $Server);
                }
            }
        }
    }

    /**
     * 玩家在线连接数
     * @param $uid
     * @return int
     */
    public function onlineNum($uid)
    {
        $sum = 0;
        $table = $this->getTable(self::UID_FD_KEY);
        $rowKey = $this->getRowKey(self::UID_FD_KEY, $uid);
        if ($table->exist($rowKey)) {
            $row = $table->get($rowKey, $this->fdColumnName);
            $fdArray = $this->unfmtFds($row);
            $sum = count($fdArray);
        }
        return $sum;
    }

    /**
     * 当前所有在线的uid
     * @return array
     */
    public function onlineUids()
    {
        $uids = [];
        $table = $this->getTable(self::FD_UID_KEY);
        foreach($table as $row)
        {
            $uids[] = $row['uid'];
        }
        return array_unique($uids);
    }

    /**
     * @param $fd
     * @return bool|mixed
     */
    public function fdExist($fd)
    {
        $fdTable = $this->getTable(self::FD_UID_KEY);
        return $fdTable->exist($this->getRowKey(self::FD_UID_KEY, $fd));
    }
}
