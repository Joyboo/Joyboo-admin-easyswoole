<?php

// 注册“消费队列”的自定义进程
use EasySwoole\Component\Process\Config;
use EasySwoole\Component\Process\Manager;

$component = [
    [
        'name' => 'tracker',                                  // 进程名
        'class' => \App\CustomProcess\HttpTracker::class,     // 运行类
        'psnum' => 2,                                         // 进程数, 默认1个
        'queue' => 'Report-Tracker',                          // 监听的redis队列名
        'tick' => 1000,                                       // 多久运行一次，单位毫秒, 默认1000毫秒
        'limit' => 200,                                       // 单次出队列的阈值, 默认200
        'coroutine' => false                                  // 是否为每条数据开启协程环境
    ],
];

// 查看进程状态 php easyswoole process show
(function () use ($component) {
    $group = config('SERVER_NAME') . '.my';
    foreach ($component as $value) {

        $proName = $group . '.' . $value['name'];

        $class = $value['class'];

        $psnum = intval($value['psnum'] ?? 1);

        // 统一一下索引起始值，easyswoole进程名从0开始
        for ($i = 0; $i < $psnum; ++$i) {
            $processConfig = new Config([
                'processName' => $proName . '.' . $i,
                'processGroup' => $group,
                'arg' => $value,
                'enableCoroutine' => true,
            ]);
            Manager::getInstance()->addProcess(new $class($processConfig));
        }
    }
})();

unset($component);
