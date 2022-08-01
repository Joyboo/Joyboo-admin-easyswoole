<?php


namespace App\Crontab;

use App\Model\Admin\HttpTracker;
use EasySwoole\Redis\Redis;
use EasySwoole\Redis\Config\RedisConfig;
use EasySwoole\Redis\Exception\RedisException;
use WonderGame\EsUtility\Common\Classes\Mysqli;

/**
 * 这是一个简单示例，在后台管理中添加Crontab即可运行！
 * Class Index
 * @package App\Crontab
 */
class Index
{
    public function test()
    {
        var_dump(date('Y-m-d H:i:s') .' test ok');
    }

    /**
     * 删N天前的链路追踪日志
     * @param $args
     * @return void
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public function delHttpTracker($args = [])
    {
        $days = intval($args['days'] ?? 90);
        // 最近10天不删除
        if ($days < 10)
        {
            return;
        }

        $begintime = strtotime("-{$days} days");

        /** @var HttpTracker $model */
        $model = model_admin('HttpTracker');
        $model->where('instime', $begintime, '<=')->destroy();
    }

    public function pingRedis($args = [])
    {
        $config = config('REDIS');
        $regKeys = array_keys($config);

        $pool = isset($args['pool']) ? explode(',', $args['pool']) : $regKeys;

        $warning = [];
        foreach ($config as $sys => $cfg)
        {
            if ( ! empty($pool) && ! in_array($sys, $pool))
            {
                continue;
            }
            try {
                $RedisConfig = new RedisConfig($cfg);
                $redis = new Redis($RedisConfig);

                $ping = $redis->ping();
                if ( ! $ping) {
                    throw new RedisException('redis ping 失败');
                }
                $redis->disconnect();
            }
            catch (RedisException | \Exception | \Throwable $e)
            {
                // 删掉密码
                unset($cfg['auth']);
                $warning[] = "连接名：$sys, 系统，参数: " . json_encode($cfg) . ", 详情: " . $e->getMessage();
                if (isset($redis) && $redis instanceof Redis) {
                    $redis->disconnect();
                }
            }
        }
        if ($warning) {
            $this->doWarning(count($warning) . '个Redis实例状态异常', $warning);
        }
    }

    public function pingMysql($args = [])
    {
        $config = config('MYSQL');
        $regKeys = array_keys($config);

        $pool = isset($args['pool']) ? explode(',', $args['pool']) : $regKeys;

        foreach ($pool as $name)
        {
            if ( ! in_array($name, $regKeys))
            {
                continue;
            }
            try {
                $Mysqli = new Mysqli($name);
                $ping = $Mysqli->rawQuery('select 1');
                if ( ! $ping) {
                    throw new \EasySwoole\Mysqli\Exception\Exception('mysql ping 失败');
                }
                $Mysqli->close();
            }
            catch (\EasySwoole\Mysqli\Exception\Exception | \Exception | \Throwable $e)
            {
                $cfg = $config[$name];
                unset($cfg['password']);
                $warning[] = "连接名：$name, 参数：" . json_encode($cfg) . ", 详情: " . $e->getMessage();
                if (isset($Mysqli) && $Mysqli instanceof Mysqli) {
                    $Mysqli->close();
                }
            }
        }

        if ($warning) {
            $this->doWarning(count($warning) . '个Mysql连接状态异常', $warning);
        }
    }

    /**
     * 执行异常通知
     * @param $title
     * @param $array
     * @return void
     */
    protected function doWarning($title = '', $array = [])
    {
        wechat_notice($title, implode(',', $array), '#e6a23c');

        $message = "### **{$title}**" . $this->wrap;
        foreach ($array as $item)
        {
            $message .= "- $item " . $this->wrap;
        }
        dingtalk_markdown($title, $message);
    }
}
