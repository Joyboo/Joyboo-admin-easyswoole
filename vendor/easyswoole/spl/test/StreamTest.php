<?php
/**
 * @CreateTime:   2019/9/9 下午06:45
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2019) Easyswoole all rights reserved
 * @Description:  SplStream 单元测试
 */
namespace EasySwoole\Spl\Test;

use EasySwoole\Spl\SplStream;
use EasySwoole\Spl\Test\Stream\TestStream;
use PHPUnit\Framework\TestCase;

class StreamTest extends TestCase {

    public function testConstruct() {
        $resource = fopen(getcwd().'/tmp.txt', 'a+');
        $stream = new SplStream($resource);
        $stream->truncate();
        $stream->seek(0);
        $stream->write('Easyswoole');
        $this->assertEquals(
            'Easyswoole',
            $stream->__toString()
        );

        $stream = new SplStream(new TestStream());
        $this->assertEquals(
            'EsObject',
            $stream->__toString()
        );

        $stream = new SplStream('Es');
        $this->assertEquals(
            'Es',
            $stream->__toString()
        );

    }

    public function testToString() {
        $stream = new SplStream('Es');
        $this->assertEquals(
            'Es',
            $stream->__toString()
        );
    }

    public function testClose() {
        $resource = fopen(getcwd().'/tmp.txt', 'ab+');
        $stream = new SplStream($resource);
        $stream->close();
        $this->assertEquals('', $stream->__toString());
    }

    public function testDetach() {
        $stream = new SplStream('Es');
        $stream->detach();
        // 抛异常，所以返回为''
        $this->assertEquals('', $stream->__toString());
    }

    public function testGetSize() {
        $stream = new SplStream('Es');
        $this->assertEquals(2, $stream->getSize());
    }

    public function testTell() {
        $stream = new SplStream('Es');
        $stream->seek(1);
        $this->assertEquals(1, $stream->tell());
    }

    public function testEof() {
        $stream = new SplStream('Es');
        $stream->seek(1);
        $this->assertNotTrue($stream->eof());
    }

    public function testIsSeekable() {
        $stream = new SplStream('Es');
        $this->assertTrue($stream->isSeekable());
    }

    public function testSeek() {
        $stream = new SplStream('Es');
        $stream->seek(1);
        $this->assertEquals(1, $stream->tell());
    }

    public function testRewind() {
        $stream = new SplStream('Es');
        $stream->rewind();
        $this->assertEquals(0, $stream->tell());
    }

    public function testIsWritable() {
        $stream = new SplStream('Es');
        $this->assertEquals(true, $stream->isWritable());
    }

    public function testWrite() {
        $stream = new SplStream('');
        $stream->write('Es');
        $this->assertEquals('Es', $stream->__toString());
    }

    public function testIsReadable() {
        $stream = new SplStream('Es');
        $this->assertTrue($stream->isReadable());
    }

    public function testRead() {
        $resource = fopen(getcwd().'/tmp.txt', 'a+');
        $stream = new SplStream($resource);
        $stream->truncate();
        $stream->seek(0);
        $stream->write('Es');
        $stream->seek(0);
        $this->assertEquals('E', $stream->read(1));
    }

    public function testGetContents() {
        $stream = new SplStream('Es');
        $stream->seek(0);
        $this->assertEquals('Es', $stream->getContents());
    }

    public function testGetMetadata() {
        $stream = new SplStream('Es');
        $this->assertEquals('MEMORY', $stream->getMetadata()['stream_type']);
    }

    public function testGetStreamResource() {
        $stream = new SplStream('Es');
        $source = $stream->getStreamResource();
        fseek($source, 0, SEEK_SET);
        $this->assertEquals('Es', stream_get_contents($source));
    }

    public function testTruncate() {
        $stream = new SplStream('Es');
        $stream->truncate(1);
        $this->assertEquals('E', $stream->__toString());
    }
}