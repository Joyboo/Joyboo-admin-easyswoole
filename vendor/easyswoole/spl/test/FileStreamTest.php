<?php
/**
 * @CreateTime:   2019/9/14 下午10:24
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2019) Easyswoole all rights reserved
 * @Description:  SplFileStream 单元测试
 */
namespace EasySwoole\Spl\Test;

use EasySwoole\Spl\SplFileStream;
use PHPUnit\Framework\TestCase;

class FileStreamTest extends TestCase {

    public function testConstruct() {
        $fileStream = new SplFileStream('./test.txt');
        $this->assertEquals('STDIO', $fileStream->getMetadata('stream_type'));
    }

    public function testLock() {
        $fileStream = new SplFileStream('./test.txt');
        $this->assertTrue($fileStream->lock());
    }

    public function testUnlock() {
        $fileStream = new SplFileStream('./test.txt');
        $this->assertTrue($fileStream->unlock());
    }
}