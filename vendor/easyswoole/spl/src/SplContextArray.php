<?php


namespace EasySwoole\Spl;


use Swoole\Coroutine;

class SplContextArray implements \ArrayAccess,\Countable ,\JsonSerializable ,\IteratorAggregate
{

    private $data = [];
    private $autoClear = false;
    private $onContextCreate;
    private $onContextDestroy;

    function __construct(bool $autoClear = true)
    {
        $this->autoClear = $autoClear;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->data[$this->cid()]);
    }


    public function offsetExists($offset)
    {
        return isset($this->data[$this->cid()][$offset]);
    }

    public function offsetGet($offset)
    {
        if(isset($this->data[$this->cid()][$offset])){
            return $this->data[$this->cid()][$offset];
        }
        return null;
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$this->cid()][$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$this->cid()][$offset]);
    }


    /************** Count *************/

    public function count()
    {
        return count($this->data[$this->cid()]);
    }


    function destroy(int $cid = null)
    {
        if($cid === null){
            $cid = Coroutine::getCid();
        }
        if($this->onContextDestroy){
            call_user_func($this->onContextDestroy,$this);
        }
        unset($this->data[$cid]);
    }

    public function jsonSerialize()
    {
        return $this->data[$this->cid()];
    }

    public function toArray():array
    {
        return $this->data[$this->cid()];
    }

    public function loadArray(array $array)
    {
        $this->data[$this->cid()] = $array;
    }

    public function setOnContextCreate(callable $call)
    {
        $this->onContextCreate = $call;
        return $this;
    }

    public function setOnContextDestroy(callable $call)
    {
        $this->onContextDestroy = $call;
        return $this;
    }

    private function cid():int
    {
        $cid = Coroutine::getCid();
        if(!isset($this->data[$cid])){
            $this->data[$cid] = [];
            if($this->onContextCreate){
                call_user_func($this->onContextCreate,$this);
            }
            if($this->autoClear && $cid > 0){
                defer(function ()use($cid){
                    $this->destroy($cid);
                });
            }
        }
        return $cid;
    }
}