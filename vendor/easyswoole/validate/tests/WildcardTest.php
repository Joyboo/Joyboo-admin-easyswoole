<?php


namespace EasySwoole\Validate\tests;


use EasySwoole\Validate\Validate;

class WildcardTest extends BaseTestCase
{
    public function testLeft()
    {
        $this->freeValidate();
        $this->validate->addColumn('*.a')->required()->notEmpty()->between(1, 10);
        $this->assertFalse($this->validate->validate([
            'a' => ['a' => 2],
            'b' => ['a' => 11]
        ]));
        $this->assertEquals('*.a只能在 1 - 10 之间', $this->validate->getError()->getErrorRuleMsg());

        $this->freeValidate();
        $this->validate->addColumn('*.a')->required()->notEmpty()->between(1, 10);
        $this->assertTrue($this->validate->validate([
            'a' => ['a' => 2],
            'b' => ['a' => 9]
        ]));

        $validate = Validate::make([
            '*.a' => 'required|notEmpty|between:1,10'
        ]);
        $this->assertFalse($validate->validate([
            'a' => ['a' => 2],
            'b' => ['a' => 11]
        ]));
        $this->assertEquals('*.a只能在 1 - 10 之间', $validate->getError()->getErrorRuleMsg());
    }

    public function testMiddle()
    {
        $this->freeValidate();
        $this->validate->addColumn('a.*.a')->required()->notEmpty()->between(1, 10);
        $this->assertFalse($this->validate->validate([
            'a' => ['a' => ['a' => 0]],
            'b' => ['a' => 11]
        ]));
        $this->assertEquals('a.*.a只能在 1 - 10 之间', $this->validate->getError()->getErrorRuleMsg());

        $this->freeValidate();
        $this->validate->addColumn('a.*.a')->required()->notEmpty()->between(1, 10);
        $this->assertTrue($this->validate->validate([
            'a' => ['a' => ['a' => 1]],
            'b' => ['a' => 11]
        ]));

        $validate = Validate::make([
            'a.*.a' => 'required|notEmpty|between:1,10'
        ], [
            'a.*.a.between' => '不在1-10之间'
        ]);
        $this->assertFalse($validate->validate([
            'a' => ['a' => ['a' => 11]],
            'b' => ['a' => 11]
        ]));
        $this->assertEquals('不在1-10之间', $validate->getError()->getErrorRuleMsg());
    }
}
