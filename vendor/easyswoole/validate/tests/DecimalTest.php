<?php

namespace EasySwoole\Validate\tests;

/**
 * @internal
 */
class DecimalTest extends BaseTestCase
{
    /*
   * 合法
   */
    public function testValidCase()
    {
        $this->freeValidate();
        $this->validate->addColumn('no')->decimal();
        $bool = $this->validate->validate(['no' => 1111]);
        $this->assertTrue($bool);

        $this->freeValidate();
        $this->validate->addColumn('no')->decimal(1);
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
        $this->validate->addColumn('no')->decimal(2);
        $bool = $this->validate->validate(['no' => 1234]);
        $this->assertFalse($bool);
        $this->assertEquals('no只能是小数', $this->validate->getError()->__toString());
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $this->freeValidate();
        $this->freeValidate();
        $this->validate->addColumn('no')->decimal(2, 'no只能是小数');
        $bool = $this->validate->validate(['no' => 1161709455.999]);
        $this->assertFalse($bool);
        $this->assertEquals('no只能是小数', $this->validate->getError()->__toString());
    }
}
