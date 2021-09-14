<?php

namespace EasySwoole\Validate\tests;

/**
 * 数字数字用例
 * Class NumericTest
 *
 * @internal
 */
class NumericTest extends BaseTestCase
{
    /*
     * 合法
     */
    public function testValidCase()
    {
        /*
         * int
         */
        $this->freeValidate();
        $this->validate->addColumn('age')->numeric();
        $bool = $this->validate->validate(['age' => 12]);
        $this->assertTrue($bool);

        /*
         * float
         */
        $this->freeValidate();
        $this->validate->addColumn('price')->numeric();
        $bool = $this->validate->validate(['price' => 2.3]);
        $this->assertTrue($bool);

        /*
         * 字符整数
         */
        $this->freeValidate();
        $this->validate->addColumn('age')->numeric();
        $bool = $this->validate->validate(['age' => '12']);
        $this->assertTrue($bool);

        /*
         * 字符小数
         */
        $this->freeValidate();
        $this->validate->addColumn('price')->numeric();
        $bool = $this->validate->validate(['price' => '2.3']);
        $this->assertTrue($bool);
    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        /*
         * 非数字
         */
        $this->freeValidate();
        $this->validate->addColumn('price', '价格')->numeric();
        $bool = $this->validate->validate(['price' => 'blank']);
        $this->assertFalse($bool);
        $this->assertEquals('价格只能是数字类型', $this->validate->getError()->__toString());
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        /*
         * 非数字
         */
        $this->freeValidate();
        $this->validate->addColumn('price')->numeric('价格必须是数字');
        $bool = $this->validate->validate(['price' => 'blank']);
        $this->assertFalse($bool);
        $this->assertEquals('价格必须是数字', $this->validate->getError()->__toString());
    }
}
