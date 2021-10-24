<?php


namespace App\Crontab;

use EasySwoole\Task\AbstractInterface\TaskInterface;

/**
 * 异步任务模板类
 * Class Template
 * @package App\Crontab
 */
class Template implements TaskInterface
{
    protected $eclass = '';
    protected $method = '';

    protected $args = [];

    public function __construct($attr, $args)
    {
        // 保存投递过来的数据
        list($this->eclass, $this->method) = $attr;
        $this->args = $args;
    }

    public function onException(\Throwable $throwable, int $taskId, int $workerIndex)
    {
        // 异常处理
        throw $throwable;
    }

    public function run(int $taskId, int $workerIndex)
    {
        $data = $this->args;
        if (!is_array($data)) {
            trace(__METHOD__ . "仅支持数组传参, data:" . var_export($data, true), 'error');
            return;
        }

        foreach ($data as $k => $v) {
            // 解析指定函数
            if (preg_match('/date\(|time\(|strtotime\(/i', $v)) {
                eval('$data["' . $k .'"] = ' . $v . ';');
            }
        }

        $className = "\\App\\Crontab\\" . ucfirst($this->eclass);

        try {
            if (!class_exists($className)) {
                trace("$className Not Found!", 'error');
                return;
            }

            if ( ! method_exists($className, $this->method)) {
                trace("{$className}->{$this->method} Not Found!", 'error');
                return;
            }

            (new $className())->{$this->method}($data);
        } catch (\Exception | \Throwable $e) {
            trace($e->getMessage(), 'error');
            return $e->getMessage();
        }

        return "success: $className -> {$this->method}";
    }
}
