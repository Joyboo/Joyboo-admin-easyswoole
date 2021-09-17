<?php

namespace EasySwoole\EasySwoole;

use App\Websocket\Events;
use App\Websocket\Parser;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\RedisPool\RedisPool;
use EasySwoole\Socket\Dispatcher;
use EasySwoole\Socket\Config as SocketConfig;
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

        // mysql连接池
        $mysqlCfg = config('MYSQL');
        foreach ($mysqlCfg as $mname => $mvalue)
        {
            $MysqlConfig = new ORMConfig($mvalue);
            DbManager::getInstance()->addConnection(new Connection($MysqlConfig), $mname);
        }

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
