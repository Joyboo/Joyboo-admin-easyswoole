<?php

namespace EasySwoole\Validate\tests;

/**
 * 时间戳测试用例
 * Class TimestampTest
 *
 * @internal
 */
class TimestampTest extends BaseTestCase
{
    /*
     * 合法
     */
    public function testValidCase()
    {
        $this->freeValidate();
        $this->validate->addColumn('time')->timestamp();
        $bool = $this->validate->validate(['time' => time()]);
        $this->assertTrue($bool);
    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        $this->freeValidate();
        $this->validate->addColumn('time')->timestamp();
        $bool = $this->validate->validate(['time' => 'blank']);
        $this->assertFalse($bool);
        $this->assertEquals('time必须是一个有效的时间戳', $this->validate->getError()->__toString());
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $this->freeValidate();
        $this->validate->addColumn('time')->timestamp('无效时间戳');
        $bool = $this->validate->validate(['time' => 'blank']);
        $this->assertFalse($bool);
        $this->assertEquals('无效时间戳', $this->validate->getError()->__toString());
    }
}
