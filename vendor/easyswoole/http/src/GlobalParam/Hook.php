<?php


namespace EasySwoole\Http\GlobalParam;


use EasySwoole\Component\Context\ContextManager;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\Session\Session;
use EasySwoole\Spl\SplContextArray;
use EasySwoole\Utility\Random;
use Swoole\Coroutine;

class Hook
{
    const SESSION_CONTEXT = '_SESSION_CONTEXT_';
    /** @var Session */
    protected $session;
    /** @var SessionConfig */
    protected $sessionConfig;

    public function enableSession(Session $session):SessionConfig
    {
        $this->session = $session;
        $this->sessionConfig = new SessionConfig();
        return $this->sessionConfig;
    }

    public function register()
    {
        global $_GET;
        if(!$_GET instanceof SplContextArray){
            $_GET = new SplContextArray();
        }
        global $_COOKIE;
        if(!$_COOKIE instanceof SplContextArray){
            $_COOKIE = new SplContextArray();
        }
        global $_POST;
        if(!$_POST instanceof SplContextArray){
            $_POST = new SplContextArray();
        }
        global $_FILES;
        if(!$_FILES instanceof SplContextArray){
            $_FILES = new SplContextArray();
        }
        global $_SERVER;
        if(!$_SERVER instanceof SplContextArray){
            $_SERVER = new SplContextArray();
        }
        if ($this->session){
            global $_SESSION;
            if(!$_SESSION instanceof SplContextArray){
                $_SESSION = new SplContextArray();
            }
        }
    }

    public function onRequest(Request $request,Response $response)
    {
        global $_GET;
        /** @var $_GET SplContextArray */
        $_GET->loadArray($request->getQueryParams());
        global $_COOKIE;
        /** @var $_COOKIE SplContextArray */
        $_COOKIE->loadArray($request->getCookieParams());
        global $_POST;
        /** @var $_POST SplContextArray */
        $_POST->loadArray($request->getParsedBody());
        global $_FILES;
        $files = [];
        if(!empty($request->getSwooleRequest()->files)){
            $files = $request->getSwooleRequest()->files;
        }
        /** @var $_FILES SplContextArray */
        $_FILES->loadArray($files);
        global $_SERVER;
        $server = [];
        foreach ($request->getSwooleRequest()->header as $key => $value) {
            $server['HTTP_' . strtoupper(str_replace('-', '_', $key))] = $value;
        }
        foreach ($request->getSwooleRequest()->server as $key => $value) {
            $server[strtoupper(str_replace('-', '_', $key))] = $value;
        }
        /** @var $_SERVER SplContextArray */
        $_SERVER->loadArray($server);
        if ($this->session){
            /** @var $_SESSION SplContextArray */
            global $_SESSION;
            $sid = $request->getCookieParams($this->sessionConfig->getSessionName());
            if(empty($sid)){
                $sid = Random::makeUUIDV4();
                $response->setCookie($this->sessionConfig->getSessionName(),$sid);
            }
            $context = $this->session->create($sid);
            ContextManager::getInstance()->set(self::SESSION_CONTEXT,$context);
            $_SESSION->loadArray($context->allContext());
            Coroutine::defer(function ()use($context,$sid){
                /** @var $_SESSION SplContextArray */
                global $_SESSION;
                try{
                    $context->setData($_SESSION->toArray());
                }catch (\Throwable $throwable){
                    throw $throwable;
                } finally {
                    $this->session->close($sid);
                }
            });
        }
    }
}