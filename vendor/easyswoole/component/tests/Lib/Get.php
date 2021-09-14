<?php


namespace EasySwoole\Component\Tests\Lib;


class Get
{
    public $bar;
    public $foo;

    public function __construct(Bar $bar, $foo = 1)
    {
        $this->bar = $bar;
        $this->foo = $foo;
    }
}
