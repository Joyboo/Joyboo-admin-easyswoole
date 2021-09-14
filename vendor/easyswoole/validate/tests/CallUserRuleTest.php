<?php

namespace EasySwoole\Validate\tests;

use EasySwoole\Validate\Functions\AbstractValidateFunction;
use EasySwoole\Validate\Validate;

class CustomValidator extends AbstractValidateFunction
{
    /**
     * 返回当前校验规则的名字
     */
    public function name(): string
    {
        return 'mobile';
    }

    /**
     * 失败里面做异常
     * @param $itemData
     * @param $arg
     * @param $column
     */
    public function validate($itemData, $arg, $column, Validate $validate): bool
    {
        $regular = '/^((13[0-9])|(14[5,7,9])|(15[^4])|(18[0-9])|(17[0,1,3,5,6,7,8]))\\d{8}$/';
        if (!preg_match($regular, $itemData)) {
            return false;
        }

        return true;
    }
}

/**
 * @internal
 */
class CallUserRuleTest extends BaseTestCase
{
    // 合法断言
    public function testValidCase()
    {
        $this->freeValidate();
        $this->validate->addFunction(new CustomValidator());
        $this->validate->addColumn('mobile')->callUserRule(new CustomValidator(), '手机号验证未通过');
        $validateResult = $this->validate->validate([
            'mobile' => '13312345678',
        ]);
        $this->assertTrue($validateResult);
    }

    // 默认错误信息断言
    public function testDefaultErrorMsgCase()
    {
        // 手机号验证不通过
        $this->freeValidate();
        $this->validate->addFunction(new CustomValidator());
        $this->validate->addColumn('mobile')->callUserRule(new CustomValidator(), '手机号验证未通过');
        $validateResult = $this->validate->validate(['mobile' => '12312345678']);
        $this->assertFalse($validateResult);
        $this->assertEquals('手机号验证未通过', $this->validate->getError()->__toString());
    }

    // 自定义错误信息断言
    public function testCustomErrorMsgCase()
    {
        // 日期相等
        $this->freeValidate();
        $this->validate->addFunction(new CustomValidator());
        $this->validate->addColumn('mobile')->callUserRule(new CustomValidator(), '手机号格式错误');
        $validateResult = $this->validate->validate(['mobile' => '12312345678']);
        $this->assertFalse($validateResult);
        $this->assertEquals('手机号格式错误', $this->validate->getError()->__toString());
    }
}
