<?php

namespace EasySwoole\Validate\tests;

/**
 * 必填测试用例
 * Class RequiredTest
 *
 * @internal
 */
class RequiredTest extends BaseTestCase
{
    /*
     * 合法
     */
    public function testValidCase()
    {
        $this->freeValidate();
        $this->validate->addColumn('phone')->required();
        $bool = $this->validate->validate(['phone' => '18959261286']);
        $this->assertTrue($bool);
    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        $this->freeValidate();
        $this->validate->addColumn('phone')->required();
        $bool = $this->validate->validate([]);
        $this->assertFalse($bool);
        $this->assertEquals('phone必须填写', $this->validate->getError()->__toString());
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $this->freeValidate();
        $this->validate->addColumn('phone')->required('手机号码必填');
        $bool = $this->validate->validate([]);
        $this->assertFalse($bool);
        $this->assertEquals('手机号码必填', $this->validate->getError()->__toString());
    }
}
