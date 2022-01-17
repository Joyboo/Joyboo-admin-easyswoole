<?php


namespace App\Task;

use App\Common\Classes\FdManager;
use App\Model\Admin;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Redis\Redis;
use EasySwoole\Task\AbstractInterface\TaskInterface;

abstract class Base implements TaskInterface
{
    protected $data;

    /**
     * @var \Swoole\Http\Server|\Swoole\Server|\Swoole\Server\Port|\Swoole\WebSocket\Server|null
     */
    protected $Server = null;

    /**
     * @var Redis|null
     */
    protected $Redis = null;

    public function __construct($data)
    {
        // 保存投递过来的数据
        $this->data = $data;
    }

    public function run(int $taskId, int $workerIndex)
    {
        // 放在__construct里会报错，子类如果需要使用Redis或Server,请parent::run()
        $this->Redis = defer_redis();
        $this->Server = ServerManager::getInstance()->getSwooleServer();
    }

    public function onException(\Throwable $throwable, int $taskId, int $workerIndex)
    {
        // 异常处理
    }

    /**
     * 推送给指定管理员
     * @param $uid
     * @param $data 推送数据
     * @return bool|mixed
     */
    protected function pushByUid($uid, $data)
    {
        $table = FdManager::getInstance();
        $fd = $table->getFdByUid($uid);
        if (!$fd) {
            return false;
        }
        if (!$this->Server->isEstablished($fd))
        {
            return false;
        }
        if (is_array($data)) {
            $data = json_encode($data);
        }
        return $this->Server->push($fd, $data);
    }

    /**
     * 遍历管理员并推送消息
     * @param array $where
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    protected function toAllAdmin($data, $where = [])
    {
        if (empty($where))
        {
            $where = ['status' => 1];
        }
        /** @var Admin $AdminModel */
        $AdminModel = model('Admin');
        $admins = $AdminModel->where($where)->all();

        /** @var Admin $admin */
        foreach ($admins as $admin)
        {
            $this->pushByUid($admin['id'], $data);
        }
    }
}
