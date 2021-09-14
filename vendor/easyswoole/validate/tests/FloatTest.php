<?php

namespace EasySwoole\Validate\tests;

/**
 * @internal
 */
class FloatTest extends BaseTestCase
{
    // 合法断言
    public function testValidCase()
    {
        // 小数位浮点数
        $this->freeValidate();
        $this->validate->addColumn('float')->float();
        $validateResult = $this->validate->validate(['float' => 0.001]);
        $this->assertTrue($validateResult);

        // 字符串表达
        $this->freeValidate();
        $this->validate->addColumn('float')->float();
        $validateResult = $this->validate->validate(['float' => '0.001']);
        $this->assertTrue($validateResult);

        // 整数作为浮点数
        $this->freeValidate();
        $this->validate->addColumn('float')->float();
        $validateResult = $this->validate->validate(['float' => 2]);
        $this->assertTrue($validateResult);
    }

    // 默认错误信息断言
    public function testDefaultErrorMsgCase()
    {
        // 不是合法的浮点值
        $this->freeValidate();
        $this->validate->addColumn('float')->float();
        $validateResult = $this->validate->validate(['float' => 'aaa']);
        $this->assertFalse($validateResult);
        $this->assertEquals('float只能是浮点数', $this->validate->getError()->__toString());
    }

    // 自定义错误信息断言
    public function testCustomErrorMsgCase()
    {
        $this->freeValidate();
        $this->validate->addColumn('float')->float('请输入一个浮点数');
        $validateResult = $this->validate->validate(['float' => 'a']);
        $this->assertFalse($validateResult);
        $this->assertEquals('请输入一个浮点数', $this->validate->getError()->__toString());
    }
}
