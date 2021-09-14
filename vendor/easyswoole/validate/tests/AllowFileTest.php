<?php

namespace EasySwoole\Validate\tests;

/**
 * @internal
 */
class AllowFileTest extends BaseTestCase
{
    public function testValidateClass()
    {
        $this->freeValidate();
        $this->validate->addColumn('file')->allowFile(['png']);
        $bool = $this->validate->validate(['file' => (new UploadFile(__DIR__ . '/../res/easyswoole.png', 1, 200, 'easyswoole.png'))]);
        $this->assertTrue($bool);

        $this->freeValidate();
        $this->validate->addColumn('file')->allowFile(['jpg', 'mp4']);
        $bool = $this->validate->validate(['file' => (new UploadFile(__DIR__ . '/../res/easyswoole.png', 1, 200, 'easyswoole.png'))]);
        $this->assertFalse($bool);
        $this->assertEquals('file文件扩展名必须在[jpg,mp4]内', $this->validate->getError()->__toString());
    }
}
