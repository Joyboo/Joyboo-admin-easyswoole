<?php

namespace EasySwoole\Validate\tests;

/**
 * @internal
 */
class AlphaDashTest extends BaseTestCase
{
    /*
    * 合法
    */
    public function testValidCase()
    {
        $this->freeValidate();
        $this->validate->addColumn('no')->alphaDash();
        $bool = $this->validate->validate(['no' => 'A_1161709455']);
        $this->assertTrue($bool);
    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        $this->freeValidate();
        $this->freeValidate();
        $this->validate->addColumn('no')->alphaDash();
        $bool = $this->validate->validate(['no' => '1161709455.999']);
        $this->assertFalse($bool);
        $this->assertEquals('no只能是字母数字下划线和破折号', $this->validate->getError()->__toString());
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $this->freeValidate();
        $this->freeValidate();
        $this->validate->addColumn('no')->alphaDash('学号只能由字母数字下划线和破折号构成');
        $bool = $this->validate->validate(['no' => '1161709455.999']);
        $this->assertFalse($bool);
        $this->assertEquals('学号只能由字母数字下划线和破折号构成', $this->validate->getError()->__toString());
    }
}
