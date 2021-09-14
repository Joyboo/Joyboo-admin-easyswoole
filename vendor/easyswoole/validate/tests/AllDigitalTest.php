<?php

namespace EasySwoole\Validate\tests;

/**
 * @internal
 */
class AllDigitalTest extends BaseTestCase
{
    /*
     * 合法
     */
    public function testValidCase()
    {
        $this->freeValidate();
        $this->validate->addColumn('no')->allDigital();
        $bool = $this->validate->validate(['no' => '1161709455']);
        $this->assertTrue($bool);
    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        $this->freeValidate();
        $this->freeValidate();
        $this->validate->addColumn('no')->allDigital();
        $bool = $this->validate->validate(['no' => '1161709455.999']);
        $this->assertFalse($bool);
        $this->assertEquals('no只能由数字构成', $this->validate->getError()->__toString());
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $this->freeValidate();
        $this->freeValidate();
        $this->validate->addColumn('no')->allDigital('学号只能由数字构成');
        $bool = $this->validate->validate(['no' => '1161709455.999']);
        $this->assertFalse($bool);
        $this->assertEquals('学号只能由数字构成', $this->validate->getError()->__toString());
    }
}
