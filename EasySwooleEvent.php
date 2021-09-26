<?php

namespace EasySwoole\EasySwoole;

use App\Websocket\Events;
use App\Websocket\Parser;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\RedisPool\RedisPool;
use EasySwoole\Socket\Dispatcher;
use EasySwoole\Socket\Config as SocketConfig;
use EasySwoole\Utility\File;
use Swoole\Websocket\Server as WSserver;
use Swoole\WebSocket\Frame;
use EasySwoole\Redis\Config\RedisConfig;
use EasySwoole\ORM\Db\Config as ORMConfig;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\ORM\DbManager;
use EasySwoole\I18N\I18N;
use EasySwoole\Component\Di;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
//        date_default_timezone_set('Asia/Shanghai');
        // 注册异常处理器
        \EasySwoole\EasySwoole\Trigger::getInstance(new \App\Common\Handler\Trigger());

        $files = File::scanDirectory(EASYSWOOLE_ROOT . '/App/Common/Config');
        if (is_array($files))
        {
            foreach ($files['files'] as $file)
            {
                Config::getInstance()->loadFile($file);
            }
        }

        // mysql连接池
        $mysqlCfg = config('MYSQL');
        foreach ($mysqlCfg as $mname => $mvalue)
        {
            $MysqlConfig = new ORMConfig($mvalue);
            DbManager::getInstance()->addConnection(new Connection($MysqlConfig), $mname);
        }

        DbManager::getInstance()->onQuery(function (
            \EasySwoole\ORM\Db\Result $result,
            \EasySwoole\Mysqli\QueryBuilder $builder,
            $start) {
            trace($builder->getLastQuery(), 'info', 'sql');
        });

        //redis连接池注册
        $redisCfg = config('REDIS');
        foreach ($redisCfg as $rname => $rvalue)
        {
            $RedisConfig = new RedisConfig($rvalue);
            RedisPool::getInstance()->register($RedisConfig, $rname);
        }

        // 注册语言包
        self::i18n();
    }

    public static function mainServerCreate(EventRegister $register)
    {
        self::setWebsocet($register);

        // 热重载
        self::hotReload();
    }

    /**
     * Websocket解析器、事件
     * @param EventRegister $register
     * @throws \EasySwoole\Socket\Exception\Exception
     */
    protected static function setWebsocet(EventRegister $register)
    {
        // 创建一个 Dispatcher 配置
        $conf = new SocketConfig();
        // 设置 Dispatcher 为 WebSocket 模式
        $conf->setType(SocketConfig::WEB_SOCKET);
        // 设置解析器对象
        $conf->setParser(new Parser());
        // 创建 Dispatcher 对象 并注入 config 对象
        $dispatch = new Dispatcher($conf);
        // 给server 注册相关事件 在 WebSocket 模式下  on message 事件必须注册 并且交给 Dispatcher 对象处理
        $register->set(EventRegister::onMessage, function (WSserver $server, Frame $frame) use ($dispatch) {
            $dispatch->dispatch($server, $frame->data, $frame);
        });
        // 注册服务事件
        $register->add(EventRegister::onOpen, [Events::class, 'onOpen']);
        $register->add(EventRegister::onClose, [Events::class, 'onClose']);
        $register->add(EventRegister::onWorkerError, [Events::class, 'onError']);
    }

    /**
     * 热重载
     * reload的是worker进程，master和manager进程不会reload
     */
    public static function hotReload()
    {
        // 只允许在开发环境热重载
        if (Core::getInstance()->runMode() === 'dev')
        {
            $watcher = new \EasySwoole\FileWatcher\FileWatcher();
            // // 设置监控规则和监控目录
            $rule = new \EasySwoole\FileWatcher\WatchRule(EASYSWOOLE_ROOT . "/App");
            $watcher->addRule($rule);
            $watcher->setOnChange(function () {
                trace('检测到文件变更， worker进程reload ...');
                ServerManager::getInstance()->getSwooleServer()->reload();
            });
            $watcher->attachServer(ServerManager::getInstance()->getSwooleServer());
        }
    }

    public static function i18n()
    {
        I18N::getInstance()->addLanguage(new \App\Common\Languages\Chinese(), 'zh');
        I18N::getInstance()->addLanguage(new \App\Common\Languages\English(), 'en');
        I18N::getInstance()->setDefaultLanguage('zh');
        Di::getInstance()->set(
            SysConst::HTTP_GLOBAL_ON_REQUEST,
            function (Request $request, Response $response) {
                // 获取 header 中 language 参数
                if ($request->hasHeader('accept-language')) {
                    $langage = $request->getHeader('accept-language');
                    if (is_array($langage)) {
                        $langage = current($langage);
                    }
                    foreach (['zh', 'en'] as $lang) {
                        if (stripos($langage, $lang) !== false) {
                            I18N::getInstance()->setDefaultLanguage($lang);
                            break;
                        }
                    }
                }

                return true;
            });
    }
}
