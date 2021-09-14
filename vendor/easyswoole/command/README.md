# Command

自定义一个`Command`
```php
<?php

class Custom implements \EasySwoole\Command\AbstractInterface\CommandInterface
{
    public function commandName(): string
    {
        return 'custom';
    }

    public function desc(): string
    {
        return '用户自定义';
    }

    public function exec(): string
    {
        /** 获取原始未变化的argv */
        \EasySwoole\Command\CommandManager::getInstance()->getOriginArgv();

        /**
         * 经过处理的数据
         * 比如 1 2 3 a=1 aa=123
         * 处理之后就变成[1, 2, 3, 'a' => 1, 'aa' => 123]
         */
        \EasySwoole\Command\CommandManager::getInstance()->getArgs();

        /**
         * 获取选项
         * 比如 --config=dev -d
         * 处理之后就是['config' => 'dev', 'd' => true]
         */
        \EasySwoole\Command\CommandManager::getInstance()->getOpts();

        /**
         * 根据下标或者键来获取值
         */
        \EasySwoole\Command\CommandManager::getInstance()->getArg('a');

        /**
         * 根据键来获取选项
         */
        \EasySwoole\Command\CommandManager::getInstance()->getOpt('config');

        /**
         * 检测在args中是否存在该下标或者键
         */
        \EasySwoole\Command\CommandManager::getInstance()->issetArg(1);

        /**
         * 检测在opts中是否存在该键
         */
        \EasySwoole\Command\CommandManager::getInstance()->issetOpt();

        return '自定义命令行执行方法';
    }

    public function help(\EasySwoole\Command\AbstractInterface\CommandHelpInterface $commandHelp): \EasySwoole\Command\AbstractInterface\CommandHelpInterface
    {
        $commandHelp->addAction('test','测试方法');
        $commandHelp->addActionOpt('-no','不输出详细信息');
        return $commandHelp;
    }
}
```

如何注册：
> 在全局`boostrap`事件中来进行注册
```php
<?php
\EasySwoole\Command\CommandManager::getInstance()->addCommand(new Custom());
```