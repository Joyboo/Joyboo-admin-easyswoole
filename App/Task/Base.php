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

    public function __construct($data)
    {
        // 保存投递过来的数据
        $this->data = $data;
    }

    public function run(int $taskId, int $workerIndex)
    {
    }

    public function onException(\Throwable $throwable, int $taskId, int $workerIndex)
    {
        trace($throwable->__toString(), 'error');
    }

    /**
     * 推送给指定管理员
     * @param $uid
     * @param $data 推送数据
     * @return bool|mixed
     */
    protected function pushByUid($uid, $data)
    {
        $Server = ServerManager::getInstance()->getSwooleServer();
        $table = FdManager::getInstance();
        $table->uidForeach($uid, function ($fd) use ($data, $Server) {

            if (is_array($data)) {
                $data = json_encode($data);
            }
            $Server->push($fd, $data);
        });
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
