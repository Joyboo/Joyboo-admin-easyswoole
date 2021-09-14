<?php

namespace EasySwoole\Validate\tests;

/**
 * 最大长度测试用例
 * Class LengthMaxTest
 *
 * @internal
 */
class LengthMaxTest extends BaseTestCase
{
    /*
     * 合法
     */
    public function testValidCase()
    {
        /*
         * int
         */
        $this->freeValidate();
        $this->validate->addColumn('name')->lengthMax(6);
        $bool = $this->validate->validate(['name' => 12]);
        $this->assertTrue($bool);

        /*
         * 字符串整数
         */
        $this->freeValidate();
        $this->validate->addColumn('name')->lengthMax(6);
        $bool = $this->validate->validate(['name' => '12']);
        $this->assertTrue($bool);

        /*
         * 数组
         */
        $this->freeValidate();
        $this->validate->addColumn('fruit')->lengthMax(6);
        $bool = $this->validate->validate(['fruit' => ['apple', 'grape', 'orange']]);
        $this->assertTrue($bool);

        /*
         * file
         */
        $this->freeValidate();
        $this->validate->addColumn('file')->lengthMax(6);
        $bool = $this->validate->validate(['file' => (new UploadFile(__DIR__ . '/../res/easyswoole.png', 5, 200))]);
        $this->assertTrue($bool);
    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        /*
         * int
         */
        $this->freeValidate();
        $this->validate->addColumn('name')->lengthMax(2);
        $bool = $this->validate->validate(['name' => 123]);
        $this->assertFalse($bool);
        $this->assertEquals('name长度不能超过2', $this->validate->getError()->__toString());

        /*
         * 字符串整数
         */
        $this->freeValidate();
        $this->validate->addColumn('name')->lengthMax(2);
        $bool = $this->validate->validate(['name' => '123']);
        $this->assertFalse($bool);
        $this->assertEquals('name长度不能超过2', $this->validate->getError()->__toString());

        /*
         * 数组
         */
        $this->freeValidate();
        $this->validate->addColumn('fruit')->lengthMax(3);
        $bool = $this->validate->validate(['fruit' => ['apple', 'grape', 'orange', 'banana']]);
        $this->assertFalse($bool);
        $this->assertEquals('fruit长度不能超过3', $this->validate->getError()->__toString());

        /*
         * 对象
         */
        $this->freeValidate();
        $this->validate->addColumn('fruit')->lengthMax(3);
        $bool = $this->validate->validate(['fruit' => (object)['apple', 'grape', 'orange', 'banana']]);
        $this->assertFalse($bool);
        $this->assertEquals('fruit长度不能超过3', $this->validate->getError()->__toString());
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $this->freeValidate();
        $this->validate->addColumn('name')->lengthMax(6, '名字长度最多6位');
        $bool = $this->validate->validate(['name' => 'EasySwoole']);
        $this->assertFalse($bool);
        $this->assertEquals('名字长度最多6位', $this->validate->getError()->__toString());
    }
}
