<?php

namespace EasySwoole\Validate\tests;

/**
 * @internal
 */
class TimestampBeforeDateTest extends BaseTestCase
{
    /*
  * 合法
  */
    public function testValidCase()
    {
        $this->freeValidate();
        $this->validate->addColumn('time')->timestampBeforeDate(date('YmdHis', time() + 1));
        $bool = $this->validate->validate(['time' => time()]);
        $this->assertTrue($bool);
    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        $this->freeValidate();
        $this->validate->addColumn('time')->timestampBeforeDate($time = date('YmdHis', time() - 1));
        $bool = $this->validate->validate(['time' => time()]);
        $this->assertFalse($bool);
        $this->assertEquals("time必须在'{$time}'之前", $this->validate->getError()->__toString());
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $this->freeValidate();
        $this->validate->addColumn('time')->timestampBeforeDate(date('YmdHis', time() - 1), '无效时间戳');
        $bool = $this->validate->validate(['time' => 'blank']);
        $this->assertFalse($bool);
        $this->assertEquals('无效时间戳', $this->validate->getError()->__toString());
    }
}
