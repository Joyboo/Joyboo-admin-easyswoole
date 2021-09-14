<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/24
 * Time: 下午4:17
 */

namespace EasySwoole\Component;


class Di
{
    use Singleton;

    private $container = [];
    private $onKeyMiss = null;
    private $alias = [];

    public function alias($alias,$key): Di
    {
        if(!array_key_exists($alias,$this->container)){
            $this->alias[$alias] = $key;
            return $this;
        }else{
            throw new  \InvalidArgumentException("can not alias a real key: {$alias}");
        }
    }

    public function setOnKeyMiss(callable $call):Di
    {
        $this->onKeyMiss = $call;
        return $this;
    }

    public function deleteAlias($alias): Di
    {
        unset($this->alias[$alias]);
        return $this;
    }

    public function set($key, $obj,...$arg):void
    {
        /*
         * 注入的时候不做任何的类型检测与转换
         * 由于编程人员为问题，该注入资源并不一定会被用到
         */
        $this->container[$key] = [
            "obj"=>$obj,
            "params"=>$arg
        ];
    }

    function delete($key):Di
    {
        unset($this->container[$key]);
        return $this;
    }

    function clear():Di
    {
        $this->container = [];
        return $this;
    }

    /**
     * @param $key
     * @return null
     * @throws \Throwable
     */
    function get($key)
    {
        if(isset($this->alias[$key])){
            $key = $this->alias[$key];
        }
        if(isset($this->container[$key])){
            $obj = $this->container[$key]['obj'];
            $params = $this->container[$key]['params'];
            if(is_object($obj) || is_callable($obj)){
                return $obj;
            }else if(is_string($obj) && class_exists($obj)){
                try{
                    $ref = new \ReflectionClass($obj);
                    if(empty($params)){
                        $constructor = $ref->getConstructor();
                        if($constructor){
                            $list = $constructor->getParameters();
                            foreach ($list as $p){
                                $class = $p->getClass();
                                if($class){
                                    $temp = $this->get($class->getName());
                                }else{
                                    $temp = $this->get($p->getName()) ?? $p->getDefaultValue();
                                }
                                $params[] = $temp;
                            }
                        }
                    }
                    $this->container[$key]['obj'] = $ref->newInstanceArgs($params);
                    return $this->container[$key]['obj'];
                }catch (\Throwable $throwable){
                    throw $throwable;
                }
            }else{
                return $obj;
            }
        }else{
            if(is_callable($this->onKeyMiss)){
                return call_user_func($this->onKeyMiss,$key);
            }
            return null;
        }
    }
}
