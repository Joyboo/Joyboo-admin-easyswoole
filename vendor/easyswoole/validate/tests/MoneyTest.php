<?php

namespace EasySwoole\Validate\tests;

/**
 * @internal
 */
class MoneyTest extends BaseTestCase
{
    /*
   * 合法
   */
    public function testValidCase()
    {
        $this->freeValidate();
        $this->validate->addColumn('no')->money(2);
        $bool = $this->validate->validate(['no' => 1111.13]);
        $this->assertTrue($bool);

        $this->freeValidate();
        $this->validate->addColumn('no')->money(1);
        $bool = $this->validate->validate(['no' => 1111.1]);
        $this->assertTrue($bool);
    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        $this->freeValidate();
        $this->freeValidate();
        $this->validate->addColumn('no')->money(2);
        $bool = $this->validate->validate(['no' => 1234]);
        $this->assertFalse($bool);
        $this->assertEquals('no必须是合法的金额', $this->validate->getError()->__toString());
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $this->freeValidate();
        $this->freeValidate();
        $this->validate->addColumn('no')->money(2, 'no必须是合法的金额!');
        $bool = $this->validate->validate(['no' => 1161709455.999]);
        $this->assertFalse($bool);
        $this->assertEquals('no必须是合法的金额!', $this->validate->getError()->__toString());
    }
}
