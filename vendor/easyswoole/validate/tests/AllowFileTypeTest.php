<?php

namespace EasySwoole\Validate\tests;

/**
 * @internal
 */
class AllowFileTypeTest extends BaseTestCase
{
    public function testValidCase()
    {
        $this->freeValidate();
        $this->validate->addColumn('file')->allowFileType(['image/png']);
        $bool = $this->validate->validate(['file' => (new UploadFile(__DIR__ . '/../res/easyswoole.png', 1, 200, null, 'image/png'))]);
        $this->assertTrue($bool);

        $this->freeValidate();
        $this->validate->addColumn('file')->allowFileType(['image/png', 'image/jpg']);
        $bool = $this->validate->validate(['file' => (new UploadFile(__DIR__ . '/../res/easyswoole.png', 1, 200, null, 'image/jpeg'))]);
        $this->assertFalse($bool);
        $this->assertEquals('file文件类型必须在[image/png,image/jpg]内', $this->validate->getError()->__toString());
    }
}
