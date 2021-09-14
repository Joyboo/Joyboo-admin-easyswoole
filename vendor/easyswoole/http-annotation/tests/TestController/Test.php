<?php

namespace EasySwoole\HttpAnnotation\Tests\TestController;


class Test
{
    protected $string;
    protected $array;

    public function __construct($string, $array)
    {
        $this->string = $string;
        $this->array = $array;
    }

    /**
     * @return mixed
     */
    public function getArray()
    {
        return $this->array;
    }

    /**
     * @return mixed
     */
    public function getString()
    {
        return $this->string;
    }

    public function index()
    {
        return 'inject test class -> index';
    }
}