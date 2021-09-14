<?php
/**
 * @CreateTime:   2019/9/13 下午02:18
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2019) Easyswoole all rights reserved
 * @Description:  SplBean 单元测试
 */
namespace EasySwoole\Spl\Test;

use EasySwoole\Spl\Test\Bean\Shops;
use PHPUnit\Framework\TestCase;
use EasySwoole\Spl\Test\Bean\TestBean;

class BeanTest extends TestCase
{

    /**
     * 获取类所有的public和protected 成员变量
     */
    function testAllProperty() {
        $bean = new TestBean([
            'a'=>'a',
            'b'=>'b',
            'c'=>'c',
            'd_d'=>'d_d'
        ]);
        $this->assertEquals(
            ['a', 'b', 'd_d'],
            $bean->allProperty()
        );
    }

    /**
     * 过滤并转换成数组数据
     */
    function testToArray() {
        $bean = new TestBean([
            'a'=>'a',
            'b'=>'b',
            'c'=>'c',
            'd_d'=>'d_d'
        ]);
        $res = $bean->toArray(null, function ($a) {
            if (in_array($a, ['d_d'])) {
                return $a;
            }
        });
        $this->assertEquals(
            ['d_d' => 'd_d'],
            $res
        );
    }

    /**
     *获取过滤后带有字段别名的数组数据
     */
    function testToArrayWithMapping() {
        $bean = new TestBean([
            'a'=>1,
            'b'=>2,
            'c'=>3,
            'd_d'=>4
        ]);
        $res = $bean->toArrayWithMapping(['a', 'b', 'd-d'], function ($val) {
            return $val;
        });
        $this->assertEquals(
            [
                'a' => 1,
                'b' => 2,
                'd-d' => 4
            ],
            $res
        );
    }

    /**
     * 设置类属性
     */
    function testArrayToBean()
    {
        $bean = new TestBean([
            'a'=>'a',
            'b'=>'b',
            'c'=>'c',
            'd_d'=>'d_d'
        ]);
        $this->assertEquals([
            'a'=>'a',
            'b'=>'b',
            'd_d'=>'d_d'
        ],$bean->toArray());

        $this->assertEquals([
            'a'=>'a',
            'b'=>'b',
        ],$bean->toArray(['a','b']));

        $this->assertEquals([
            'a'=>'a',
            'd-d'=>'d_d'
        ],$bean->toArrayWithMapping(['a','d-d']));
    }

    /**
     * 设置类成员变量
     */
    function testAddProperty() {
        $bean = new TestBean();
        $bean->addProperty('a', 'es');
        $bean->addProperty('b', 'es');
        $bean->addProperty('d_d', 'es');
        $this->assertEquals(
            [
                'a' => 'es',
                'b' => 'es',
                'd_d' => 'es',
            ],
            $bean->toArray()
        );
    }

    /**
     * 获取类成员变量值
     */
    function testGetProperty() {
        $bean = new TestBean([
            'a'=>'a',
            'b'=>'b',
            'c'=>'c',
            'd_d'=>'d_d'
        ]);
        $this->assertEquals('a', $bean->getProperty('a'));
    }

    /**
     * 获取类成员变量集合
     */
    function testJsonSerialize() {
        $bean = new TestBean([
            'a'=>'a',
            'b'=>'b',
            'c'=>'c',
            'd_d'=>'d_d'
        ]);
        $this->assertEquals(
            [
                'a'=>'a',
                'b'=>'b',
                'd_d'=>'d_d'
            ],
            $bean->jsonSerialize()
        );
    }

    /**
     * 初始化操作
     */
    function testInitialize() {
        $bean = new TestBean([
            'a'=>'a',
            'b'=>'b',
            'c'=>'c',
            'd_d'=>'d_d'
        ]);
        $this->assertEquals(
            [
                'a'=>'a',
                'b'=>'b',
                'd_d'=>'d_d'
            ],
            $bean->jsonSerialize()
        );
    }

    /**
     * 设置keyMapping关系，也就是字段别名
     */
    function testSetKeyMapping() {
        $bean = new TestBean([
            'a'=>'a',
            'b'=>'b',
            'c'=>'c',
            'd-d'=>'d'
        ]);
        $this->assertEquals(
            [
                'a'=>'a',
                'b'=>'b',
                'd_d'=>'d'
            ],
            $bean->jsonSerialize()
        );
    }

    /**
     * 设置classMapping关系，也就是关联类
     */
//    function testSetClassMapping() {
//        return true;
//        $bean = new TestBean([
//            'a'=>'a',
//            'b'=>'b',
//            'c'=>'c',
//            'd-d'=>'d'
//        ]);
//        $this->assertEquals(
//            Shops::class,
//            get_class($bean->jsonSerialize()['shops'])
//        );
//    }

    function testRestore()
    {
        $bean = new TestBean([
            'a'=>'a',
            'b'=>'b',
            'c'=>'c',
            'd_d'=>'d_d'
        ]);

        $this->assertEquals([
            'a'=>2,
            'b'=>null,
            'd_d'=>null
        ],$bean->restore()->toArray());


        $this->assertEquals([
            'a'=>2
        ],$bean->restore()->toArray(null,$bean::FILTER_NOT_NULL));


        $bean->restore(['a'=>2,'b'=>3]);
    }

}