<?php
/**
 * @CreateTime:   2019/12/7 下午10:59
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2019) Easyswoole all rights reserved
 * @Description:  兼容php7.4约束类型单测
 */
namespace EasySwoole\Spl\Test;

use EasySwoole\Spl\Test\Bean\TestAllProperty;
use EasySwoole\Spl\Test\Bean\TestAllProperty74;
use PHPUnit\Framework\TestCase;

class AllProperty74Test extends TestCase
{
    function testGetAllProperty()
    {
        $allProperty = new TestAllProperty();
        $this->assertEquals(
            [
                'a', 'b', 'aInit', 'bInit'
            ]
        , $allProperty->allProperty());
    }

    function testGetAllProperty74()
    {
        $allProperty74 = new TestAllProperty74();
        $this->assertEquals([
            'a', 'b', 'aInit', 'bInit', 'typeA', 'typeB', 'typeInitA', 'typeInitB'
        ], $allProperty74->allProperty());
    }
}
