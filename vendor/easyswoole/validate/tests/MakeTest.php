<?php


namespace EasySwoole\Validate\tests;


use EasySwoole\Validate\Validate;

class MakeTest extends BaseTestCase
{
    protected $rules = [
        'name' => 'required|notEmpty',
        'age' => 'required|integer|between:20,30',
        'weight' => 'required|max:50'
    ];

    protected $message = [
        'name.required' => '名字不能为空呀！',
        'age' => '年龄输入有误呀！',
        'weight.max' => '体重最大不能超过50呀！'
    ];

    protected $alias = [
        'name' => '名字',
        'age' => '年龄',
        'weight' => '体重'
    ];

    public function testValidCase()
    {
        $validate = Validate::make($this->rules);
        $ret = $validate->validate([
            'name' => '史迪仔',
            'age' => 20,
            'weight' => 20
        ]);
        $this->assertTrue($ret);
    }

    public function testDefaultErrMsg()
    {
        $validate = Validate::make($this->rules);
        $ret = $validate->validate([
            'name' => '',
            'age' => 20,
            'weight' => 20
        ]);
        $this->assertFalse($ret);
        $this->assertEquals('name不能为空', $validate->getError()->getErrorRuleMsg());

        $validate = Validate::make($this->rules);
        $ret = $validate->validate([
            'name' => '史迪仔',
            'age' => 10,
            'weight' => 20
        ]);
        $this->assertFalse($ret);
        $this->assertEquals('age只能在 20 - 30 之间', $validate->getError()->getErrorRuleMsg());

        $validate = Validate::make($this->rules);
        $ret = $validate->validate([
            'name' => '史迪仔',
            'age' => 20,
            'weight' => 70
        ]);
        $this->assertFalse($ret);
        $this->assertEquals("weight的值不能大于'50'", $validate->getError()->getErrorRuleMsg());
    }

    public function testAliasErrMsg()
    {
        $validate = Validate::make($this->rules, [], $this->alias);
        $ret = $validate->validate([
            'name' => '',
            'age' => 20,
            'weight' => 20
        ]);
        $this->assertFalse($ret);
        $this->assertEquals('名字不能为空', $validate->getError()->getErrorRuleMsg());

        $validate = Validate::make($this->rules, [], $this->alias);
        $ret = $validate->validate([
            'name' => '史迪仔',
            'age' => 10,
            'weight' => 20
        ]);
        $this->assertFalse($ret);
        $this->assertEquals('年龄只能在 20 - 30 之间', $validate->getError()->getErrorRuleMsg());

        $validate = Validate::make($this->rules, [], $this->alias);
        $ret = $validate->validate([
            'name' => '史迪仔',
            'age' => 20,
            'weight' => 70
        ]);
        $this->assertFalse($ret);
        $this->assertEquals("体重的值不能大于'50'", $validate->getError()->getErrorRuleMsg());
    }

    public function testCustomErrMsg()
    {
        $validate = Validate::make($this->rules, $this->message);
        $ret = $validate->validate([
            'name' => null,
            'age' => 20,
            'weight' => 20
        ]);
        $this->assertFalse($ret);
        $this->assertEquals('名字不能为空呀！', $validate->getError()->getErrorRuleMsg());

        $validate = Validate::make($this->rules, $this->message);
        $ret = $validate->validate([
            'name' => '史迪仔',
            'age' => 10,
            'weight' => 20
        ]);
        $this->assertFalse($ret);
        $this->assertEquals('年龄输入有误呀！', $validate->getError()->getErrorRuleMsg());

        $validate = Validate::make($this->rules, $this->message);
        $ret = $validate->validate([
            'name' => '史迪仔',
            'age' => 20,
            'weight' => 70
        ]);
        $this->assertFalse($ret);
        $this->assertEquals("体重最大不能超过50呀！", $validate->getError()->getErrorRuleMsg());
    }
}
