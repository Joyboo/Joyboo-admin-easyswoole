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

    /**
     * 管理员信息
     * @var array
     */
    protected $operInfo = [];

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function setOperInfo(array $operInfo = [], $isMerge = false)
    {
        $this->operInfo = $isMerge ? array_merge_multi($this->operInfo, $operInfo) : $operInfo;
    }

    public function getOperInfo()
    {
        return $this->operInfo;
    }
}
