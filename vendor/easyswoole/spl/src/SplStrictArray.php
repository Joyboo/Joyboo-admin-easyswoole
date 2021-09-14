<?php


namespace EasySwoole\Spl;


class SplStrictArray implements \ArrayAccess ,\Countable ,\IteratorAggregate
{
    private $class;
    private $data = [];


    function __construct(string $itemClass)
    {
        $this->class = $itemClass;
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        if(isset($this->data[$offset])){
            return $this->data[$offset];
        }else{
            return null;
        }
    }

    public function offsetSet($offset, $value)
    {
        if(is_a($value,$this->class)){
            $this->data[$offset] = $value;
            return true;
        }
        throw new \Exception("StrictArray can only set {$this->class} object");
    }

    public function offsetUnset($offset)
    {
        if(isset($this->data[$offset])){
            unset($this->data[$offset]);
            return true;
        }else{
            return false;
        }
    }

    public function count()
    {
        return count($this->data);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }
}