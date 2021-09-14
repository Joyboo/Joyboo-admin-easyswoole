<?php
/**
 * @CreateTime:   2019/9/9 下午11:28
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2019) Easyswoole all rights reserved
 * @Description:  SplArray 单元测试
 */
namespace EasySwoole\Spl\Test;

use PHPUnit\Framework\TestCase;

use EasySwoole\Spl\SplArray;

class ArrayTest extends TestCase {

    /**
     * 设置参数
     *
     * @return SplArray
     * CreateTime: 2019/9/10 下午11:30
     */
    public function testSet() {
        $data = [
            'fruit' => [
                'apple' => 2,
                'orange' => 1,
                'grape' => 4
            ],
            'color' => [
                'red' => 12,
                'blue' => 8,
                'green' => 6
            ]
        ];
        $splArrayObj = new SplArray($data);
        $splArrayObj->set('fruit.apple', 3);
        $this->assertEquals(3, $splArrayObj->get('fruit.apple'));
        return $splArrayObj;
    }

    /**
     * 获取参数
     *
     * @depends clone testSet
     * @param SplArray $splArrayObj
     */
    public function testGet( SplArray $splArrayObj) {

        // 测试第一层的key
        $this->assertEquals(
            [
                'red' => 12,
                'blue' => 8,
                'green' => 6
            ],
            $splArrayObj->get('color')
        );

        // 测试第二层的key
        $this->assertEquals(
            12,
            $splArrayObj->get('color.red')
        );
    }

    /**
     * 转字符
     *
     * @depends clone testSet
     * CreateTime: 2019/9/10 下午11:29
     */
    public function testTostring(SplArray $splArrayObj) {
        $this->assertJsonStringEqualsJsonString(
            json_encode($splArrayObj, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
            $splArrayObj->__toString()
        );
    }

    /**
     * 数组的复制
     *
     * @depends clone testSet
     * @param SplArray $splArrayObj
     * CreateTime: 2019/9/10 下午11:37
     */
    public function testGetArrayCopy(SplArray $splArrayObj) {
        $this->assertEquals(
            [
                'fruit' => [
                    'apple' => 3,
                    'orange' => 1,
                    'grape' => 4
                ],
                'color' => [
                    'red' => 12,
                    'blue' => 8,
                    'green' => 6
                ]
            ],
            $splArrayObj->getArrayCopy()
        );
    }

    /**
     * 销毁数组元素
     *
     * @depends clone testSet
     * @param $splArrayObj SplArray
     * CreateTime: 2019/9/10 下午11:44
     */
    public function testUnset(SplArray $splArrayObj) {

        // 销毁red元素
        $splArrayObj->unset('color.red');
        $this->assertEquals(
            [
                'fruit' => [
                    'apple' => 3,
                    'orange' => 1,
                    'grape' => 4
                ],
                'color' => [
                    'blue' => 8,
                    'green' => 6
                ]
            ],
            $splArrayObj->getArrayCopy()
        );

        // 销毁color元素
        $splArrayObj->unset('color');
        $this->assertEquals(
            [
                'fruit' => [
                    'apple' => 3,
                    'orange' => 1,
                    'grape' => 4
                ]
            ],
            $splArrayObj->getArrayCopy()
        );
    }

    /**
     * 去除某个数据项(unset和delete方法其实是实现统一效果，因考虑旧版本用户使用情况，故而保留。)
     *
     * @depends clone testSet
     * @param SplArray $splArrayObj
     */
    public function testDelete(SplArray $splArrayObj) {
        $splArrayObj->delete('color');
        $this->assertEquals(
            [
                'fruit' => [
                    'apple' => 3,
                    'orange' => 1,
                    'grape' => 4
                ]
            ],
            $splArrayObj->getArrayCopy()
        );
    }

    /**
     * 数组值唯一
     *
     * CreateTime: 2019/9/10 下午11:55
     * @param SplArray $splArrayObj
     * @return bool
     */
    public function testUnique() {
        $splArrayObj = new SplArray(
            [
                'name1' => 'es',
                'name2' => 'es'
            ]
        );
        $this->assertEquals(
            ['name1'=>'es']
        , $splArrayObj->unique()->getArrayCopy());
    }

    /**
     * 获取数组中重复的值
     *
     * @depends clone testSet
     * CreateTime: 2019/9/11 上午12:22
     * @return bool
     */
    public function testMultiple() {
        $splArrayObj = new SplArray(
            [
                'name1' => 'es',
                'name2' => 'es'
            ]
        );
        $this->assertEquals(['name2'=>'es'], $splArrayObj->multiple()->getArrayCopy());
    }

    /**
     * 进行排序并保持索引关系
     *
     * @depends clone testSet
     * CreateTime: 2019/9/11 上午12:32
     * @param SplArray $splArrayObj
     */
    public function testAsort(SplArray $splArrayObj) {
        $this->assertEquals(
            [
                'color' => [
                    'red' => 12,
                    'blue' => 8,
                    'green' => 6
                ],
                'fruit' => [
                    'apple' => 3,
                    'orange' => 1,
                    'grape' => 4
                ]
            ]
            ,$splArrayObj->asort()->getArrayCopy()
        );
    }

    /**
     * 按照键名排序
     *
     * @depends clone testSet
     * CreateTime: 2019/9/11 上午12:35
     * @param SplArray $splArrayObj
     */
    public function testKsort(SplArray $splArrayObj) {
        $this->assertEquals(
            [
                'color' => [
                    'red' => 12,
                    'blue' => 8,
                    'green' => 6
                ],
                'fruit' => [
                    'apple' => 3,
                    'orange' => 1,
                    'grape' => 4
                ]
            ],
            $splArrayObj->ksort()->getArrayCopy()
        );
    }

    /**
     * 排序
     *
     * @depends clone testSet
     * CreateTime: 2019/9/11 上午12:35
     * @param SplArray $splArrayObj
     */
    public function testSort(SplArray $splArrayObj) {
        $this->assertEquals(
            [
                [
                    'red' => 12,
                    'blue' => 8,
                    'green' => 6
                ],
                [
                    'apple' => 3,
                    'orange' => 1,
                    'grape' => 4
                ]
            ],
            $splArrayObj->sort()->getArrayCopy()
        );
    }

    /**
     * 取得某一列
     *
     * @depends clone testSet
     * CreateTime: 2019/9/11 上午12:35
     * @param SplArray $splArrayObj
     * @return bool
     */
    public function testColumn(SplArray $splArrayObj) {
        $this->assertEquals(
            [12],
            $splArrayObj->column('red')->getArrayCopy()
        );
    }

    /**
     * 交换数组中的键和值
     */
    public function testFlip() {
        $splArrayObj = new SplArray([
            'es' => 'easyswoole'
        ]);
        $this->assertEquals(
            [
                'easyswoole' => 'es'
            ],
            $splArrayObj->flip()->getArrayCopy()
        );
    }

    /**
     * 过滤数组数据
     */
    public function testFilter() {
        $splArrayObj = new SplArray(
            [
                'apple' => 2,
                'orange' => 1,
                'grape' => 2,
                'pear' => 4,
                'banana' => 8
            ]
        );

        // 获取设置的键名
        $this->assertEquals(
            [
                'apple' => 2,
                'pear'  => 4
            ],
            $splArrayObj->filter('apple,pear', false)->getArrayCopy()
        );

        // 排除设置的键名
        $this->assertEquals(
            [
                'apple' => 2,
                'pear'  => 4
            ],
            $splArrayObj->filter('orange,grape,banana', true)->getArrayCopy()
        );
    }

    /**
     * 获取数组索引
     *
     * @depends clone testSet
     * @param SplArray $splArrayObj
     */
    public function testKeys(SplArray $splArrayObj) {
        $this->assertEquals(
            ['red', 'blue', 'green'],
            $splArrayObj->keys('color')
        );
    }

    /**
     * 获取数组中所有的值
     *
     * @depends clone testSet
     * @param SplArray $splArrayObj
     */
    public function testValues(SplArray $splArrayObj) {
        $this->assertEquals(
            [
                [
                    'apple' => 3,
                    'orange' => 1,
                    'grape' => 4
                ],
                [
                    'red' => 12,
                    'blue' => 8,
                    'green' => 6
                ]
            ],
            $splArrayObj->values()->getArrayCopy()
        );
    }

    /**
     * 清空数据
     *
     * @depends clone testSet
     * @param SplArray $splArrayObj
     */
    public function testFlush(SplArray $splArrayObj) {
        $this->assertEquals(
            [],
            $splArrayObj->flush()->getArrayCopy()
        );
    }

    /**
     * 重新加载数据
     *
     * @depends clone testSet
     * @param SplArray $splArrayObj
     */
    public function testLoadArray(SplArray $splArrayObj) {
        $this->assertEquals(
            [
                'name' => 'easyswoole'
            ],
            $splArrayObj->loadArray(
                [
                'name' => 'easyswoole'
                ]
            )->getArrayCopy()
        );
    }

    /**
     * 转化成xml
     *
     * @depends clone testSet
     * @param SplArray $splArrayObj
     */
    public function testToXML(SplArray $splArrayObj) {
        $this->assertEquals(
            "<xml><fruit><apple>3</apple><orange>1</orange><grape>4</grape></fruit><color><red>12</red><blue>8</blue><green>6</green></color></xml>\n",
            $splArrayObj->toXML()
        );
    }

    public function testMulti()
    {
        $splArray = new SplArray(
            [
                'a'=>[
                    "sum"=>'a1',
                    [
                        "sum"=>'s1',
                    ],
                    [
                        "sum"=>'s2',
                    ],
                ],
                'b'=>'b',
                'c'=>[
                    "sum"=>'c1'
                ],
            ]
        );

        $this->assertEquals([
            null,'s1','s2'
        ],$splArray->get('a.*.sum'));

        $this->assertEquals([
            'a1',null,'c1'
        ],$splArray->get('*.sum'));

        $this->assertEquals([
            'a1',
            [
                "sum"=>'s1',
            ],
            [
                "sum"=>'s2',
            ],
        ],$splArray->get('a.*'));
    }
}
