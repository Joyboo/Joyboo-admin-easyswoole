<?php

namespace EasySwoole\Spl\Test\Bean;

use EasySwoole\Spl\SplBean;
use EasySwoole\Spl\Test\Bean\Shops;

class TestBean extends SplBean
{
    public $a = 2;
    protected $b;
    private $c;
    protected $d_d;
//    protected $shops; // 测试setClassMapping

    protected function setKeyMapping(): array
    {
        return [
            'd-d'=>"d_d"
        ];
    }

//    protected function setClassMapping(): array
//    {
//        return [
//            'shops' => Shops::class
//        ];
//    }

}