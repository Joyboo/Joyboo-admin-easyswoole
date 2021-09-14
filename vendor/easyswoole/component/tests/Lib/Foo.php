<?php

namespace EasySwoole\Component\Tests\Lib;

class Foo
{
    public $bar;
    public function __construct(Bar $bar)
    {
        $this->bar = $bar;
    }
}
