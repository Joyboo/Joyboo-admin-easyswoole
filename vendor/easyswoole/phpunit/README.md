# Phpunit

Easyswoole/Phpunit 是对 Phpunit 的协程定制化封装，主要为解决自动协程化入口的问题。

## 安装 
```
composer require easyswoole/phpunit
```

## 使用
执行
```
./vendor/bin/co-phpunit tests
./vendor/bin/co-phpunit tests --no-coroutine //不带协程环境

```

> tests为你写的测试文件的目录，可以自定义

其他测试与phpunit一致