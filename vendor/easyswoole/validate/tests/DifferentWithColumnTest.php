<?php

namespace EasySwoole\Validate\tests;

/**
 * @internal
 */
class DifferentWithColumnTest extends BaseTestCase
{
    // 合法断言
    public function testValidCase()
    {
        // 值不相等
        $this->freeValidate();
        $this->validate->addColumn('name')->differentWithColumn('realName', true);
        $validateResult = $this->validate->validate(['name' => 'test', 'realName' => 'blank']);
        $this->assertTrue($validateResult);

        // 值相等,但类型不一样
        $this->freeValidate();
        $this->validate->addColumn('name')->differentWithColumn('realName', true);
        $validateResult = $this->validate->validate(['name' => '12', 'realName' => 12]);
        $this->assertTrue($validateResult);
    }

    // 默认错误信息断言
    public function testDefaultErrorMsgCase()
    {
        // 值相等
        $this->freeValidate();
        $this->validate->addColumn('name')->differentWithColumn('realName', true);
        $validateResult = $this->validate->validate(['name' => 'blank', 'realName' => 'blank']);
        $this->assertFalse($validateResult);
        $this->assertEquals('name必须不等于realName的值', $this->validate->getError()->__toString());

        // 值相等,但类型不一样
        $this->freeValidate();
        $this->validate->addColumn('name')->differentWithColumn('realName');
        $validateResult = $this->validate->validate(['name' => '123', 'realName' => 123]);
        $this->assertFalse($validateResult);
        $this->assertEquals('name必须不等于realName的值', $this->validate->getError()->__toString());

        $this->freeValidate();
        $this->validate->addColumn('bar', 'Bar')->differentWithColumn('foo');
        $this->validate->addColumn('foo', 'Foo');
        $validateResult = $this->validate->validate(['bar' => 11, 'foo' => 11]);
        $this->assertFalse($validateResult);
        $this->assertEquals('Bar必须不等于Foo的值', $this->validate->getError()->__toString());
    }

    // 自定义错误信息断言
    public function testCustomErrorMsgCase()
    {
        // 值相等但类型不符
        $this->freeValidate();
        $this->validate->addColumn('name')->differentWithColumn('realName', true, '昵称和真实姓名不能一致');
        $validateResult = $this->validate->validate(['name' => 'blank', 'realName' => 'blank']);
        $this->assertFalse($validateResult);
        $this->assertEquals('昵称和真实姓名不能一致', $this->validate->getError()->__toString());
    }
}
