# Task组件
出于更加灵活定制化的考虑，EasySwoole实现了一个基于自定义进程的Task组件，用以实现异步任务，并解决：
- 无法投递闭包任务
- 无法在TaskWorker等其他自定义进程继续投递任务
- 实现任务限流与状态监控

> 该组件可以独立使用

## 安装 
```
composer require easyswoole/task
```

## 示例代码

```php
use EasySwoole\Task\MessageQueue;
use EasySwoole\Task\Task;

require 'vendor/autoload.php';


$task = new Task();
//如果需要任务队列，则默认设置进去一个Queue驱动
$queue = new MessageQueue();
$task->getConfig()->setTaskQueue($queue);

$http = new swoole_http_server("127.0.0.1", 9501);
/*
添加服务
*/
$task->attachToServer($http);

$http->on("request", function ($request, $response)use($task){
    if(isset($request->get['sync'])){
        $ret = $task->sync(function ($taskId,$workerIndex){
            return "{$taskId}.{$workerIndex}";
        });
        $response->end("sync result ".$ret);
    }else if(isset($request->get['status'])) {
        var_dump($task->status());
    }else{
        $id = $task->async(function ($taskId,$workerIndex){
            \co::sleep(1);
            var_dump("async id {$taskId} task run");
        });
        $response->end("async id {$id} ");
    }
});

$http->start();
```

## 任务投递类型

### 闭包函数
```php
/**@var \EasySwoole\Task\Task $task **/
$ret = $task->sync(function ($taskId, $workerIndex){
    return 'the sync';
}, 5);
var_dump($ret); // the sync

$ret = $task->async(function ($taskId, $workerIndex){
    return 'the async';
},function ($reply, $taskId, $workerIndex){
    // 异步任务执行完成 会调用此函数 可忽略
    var_dump($reply); // the async
});
var_dump($ret); // taskId 当task目前正在执行任务数量大于maxRunningNum会返回 0 -1 -2 具体看Task类
```

### 任务接口
```php
class TestTask implements \EasySwoole\Task\AbstractInterface\TaskInterface {
    
    protected $data = null;

    public function __construct($data = []) {
        // 可通过构造函数传入所需要的数据 可以忽略
        $this->data = $data;
    }
    
    public function run(int $taskId,int $workerIndex){
        // TODO: Implement run() method.
        return 'success';
    }
    
    public function onException(\Throwable $throwable,int $taskId,int $workerIndex){
    // TODO: Implement onException() method.
    }
}
/**@var \EasySwoole\Task\Task $task **/
$task->sync(new TestTask([]));
$task->sync(TestTask::class); // 两种方式都可

$task->async(new TestTask([]));
$task->async(TestTask::class,function (){
    // finish 可忽略
});
```

### callable
```php
class TestTask{
    public static function testSync($taskId, $workerIndex){
    
    }
    public static function testAsync($taskId, $workerIndex){
    
    }
    public static function testFinish($taskId, $workerIndex){
    
    }
}
/**@var \EasySwoole\Task\Task $task **/
$task->sync([TestTask::class,'testSync']);
$task->async([TestTask::class,'testAsync'],[TestTask::class,'testFinish']);
```
