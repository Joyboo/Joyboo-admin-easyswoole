<?php


namespace App\Common\Classes;


use EasySwoole\Component\CoroutineSingleTon;
use EasySwoole\Http\Request;
use EasySwoole\Socket\Bean\Caller;

/**
 * 协程单例对象
 * Class CtxRequest
 * @package App\Common\Classes
 */
class CtxRequest
{
    use CoroutineSingleTon;

    /**
     * Request对象
     * @var Request|null
     */
    protected $request = null;

    /**
     * @var Caller | null
     */
    protected $caller = null;

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getOperinfo()
    {
        return $this->request instanceof Request ? $this->request->getAttribute('operinfo', []) : [];
    }

    public function __set($name, $value)
    {
        $name = strtolower($name);
        $this->{$name} = $value;
    }

    public function __get($name)
    {
        $name = strtolower($name);
        if (property_exists($this, $name)) {
            return $this->{$name};
        } else {
            $cid = Coroutine::getCid();
            throw new \Exception("[cid:{$cid}]CtxRequest Not Exists Protected: $name");
        }
    }
}
