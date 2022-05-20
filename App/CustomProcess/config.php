<?php

return [
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
