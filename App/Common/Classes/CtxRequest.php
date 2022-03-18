<?php


namespace App\Common\Classes;


use EasySwoole\Component\CoroutineSingleTon;
use EasySwoole\Http\Request;

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

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }
}
