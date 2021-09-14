<?php

namespace EasySwoole\Validate\tests;

use EasySwoole\Spl\SplArray;
use EasySwoole\Validate\Validate;

/**
 * @internal
 */
class FuncTest extends BaseTestCase
{
    // 合法断言
    public function testValidCase()
    {
        $this->freeValidate();
        $this->validate->addColumn('callback')->func(function ($itemData, $column, Validate $validate) {
            return ($validate->getVerifyData() instanceof SplArray) && $column === 'callback' && $itemData === 0.001;
        });
        $validateResult = $this->validate->validate(['callback' => 0.001]);
        $this->assertTrue($validateResult);
    }

    // 默认错误信息断言
    public function testDefaultErrorMsgCase()
    {
        $this->freeValidate();
        $this->validate->addColumn('callback', '用户名')->func(function ($data, $name) {
            return false;
        });
        $validateResult = $this->validate->validate(['callback' => 0.001]);
        $this->assertFalse($validateResult);
        $this->assertEquals('用户名自定义验证失败', $this->validate->getError()->__toString());
    }

    // 自定义错误信息断言
    public function testCustomErrorMsgCase()
    {
        $this->freeValidate();
        $this->validate->addColumn('username')->func(function ($data, $name) {
            return false;
        }, '用户不存在');
        $validateResult = $this->validate->validate(['username' => 'admin']);
        $this->assertFalse($validateResult);
        $this->assertEquals('用户不存在', $this->validate->getError()->__toString());
    }
}
