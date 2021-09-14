<?php
/**
 * @CreateTime:   2019/9/14 下午10:41
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2019) Easyswoole all rights reserved
 * @Description:  SplString 单元测试
 */
namespace EasySwoole\Spl\Test;

use EasySwoole\Spl\SplString;
use PHPUnit\Framework\TestCase;

class StringTest extends TestCase {

    public function testSetString() {
        $splString = new SplString();
        $splString->setString('Easyswoole');
        $this->assertEquals('Easyswoole', $splString->__toString());
    }

    public function testSplit() {
        $splString = new SplString('Hello, Easyswoole');
        $this->assertEquals([
            'Hello', ', Eas', 'yswoo', 'le'
        ], $splString->split(5)->getArrayCopy());
    }

    public function testExplode() {
        $splString = new SplString('Hello, Easyswoole');
        $this->assertEquals([
            'Hello', 'Easyswoole'
        ], $splString->explode(', ')->getArrayCopy());
    }

    public function testSubString() {
        $splString = new SplString('Hello, Easyswoole');
        $this->assertEquals('Hello', $splString->subString(0, 5)->__toString());
    }

    public function testEncodingConvert() {
        $splString = new SplString('Hello, Easyswoole');
        $this->assertEquals('Hello, Easyswoole', $splString->encodingConvert('UTF-8')->__toString());
    }

    public function testUtf8() {
        $splString = new SplString('Hello, Easyswoole');
        $this->assertEquals('Hello, Easyswoole', $splString->utf8()->__toString());
    }

    public function testUnicodeToUtf8() {
        $splString = new SplString('Hello, Easyswoole');
        $this->assertEquals('Hello, Easyswoole', $splString->unicodeToUtf8()->__toString());
    }

    public function testToUnicode() {
        $splString = new SplString('Hello, Easyswoole');
        $this->assertEquals('\U0048\U0065\U006C\U006C\U006F\U002C\U0020\U0045\U0061\U0073\U0079\U0073\U0077\U006F\U006F\U006C\U0065', $splString->toUnicode()->__toString());
    }

    public function testCompare() {
        $splString = new SplString('Hello, Easyswoole');
        $this->assertEquals(-18, $splString->compare('Hello, Es'));
    }

    public function testLtrim() {
        $splString = new SplString(' es');
        $this->assertEquals('es', $splString->lTrim());
    }

    public function testRtrim() {
        $splString = new SplString('es ');
        $this->assertEquals('es', $splString->rTrim());
    }

    public function testTrime() {
        $splString = new SplString(' es ');
        $this->assertEquals('es', $splString->trim());
    }

    public function testPad() {
        $splString = new SplString('Easy');
        $splString->pad(10, 'swoole');
        $this->assertEquals('Easyswoole', $splString->__toString());

        $splString->pad(16, 'Hello,', STR_PAD_LEFT);
        $this->assertEquals('Hello,Easyswoole', $splString->__toString());

        $splString->pad(18, '@', STR_PAD_BOTH);
        $this->assertEquals('@Hello,Easyswoole@', $splString->__toString());
    }

    public function testRepeat() {
        $splString = new SplString('EasySwoole');
        $splString->repeat(2);
        $this->assertEquals('EasySwooleEasySwoole', $splString->__toString());
    }

    public function testLength() {
        $splString = new SplString('EasySwoole');
        $this->assertEquals(10, $splString->length());
    }

    public function testUpper() {
        $splString = new SplString('EasySwoole');
        $this->assertEquals('EASYSWOOLE', $splString->upper());
    }

    public function testLower() {
        $splString = new SplString('EasySwoole');
        $this->assertEquals('easyswoole', $splString->lower());
    }

    public function testStripTags() {
        $splString = new SplString('<span>Easyswoole</span>');
        $this->assertEquals('Easyswoole', $splString->stripTags()->__toString());
    }

    public function testReplace() {
        $splString = new SplString('Hello, es!');
        $this->assertEquals('Hello, Easyswoole!', $splString->replace('es', 'Easyswoole'));
    }

    public function testBetween() {
        $splString = new SplString('Hello,Easyswoole');
        $this->assertEquals(',', $splString->between('Hello', 'Easyswoole')->__toString());
    }

    public function testRegex() {
        $splString = new SplString('Hello,Easyswoole');
        $this->assertEquals('Easyswoole', $splString->regex('/Easyswoole/'));
    }

    public function testExist() {
        $splString = new SplString('Hello,Easyswoole');
        $this->assertTrue($splString->exist('Easyswoole'));
    }

    public function testKebab() {
        $splString = new SplString('EasySwoole');
        $this->assertEquals('easy-swoole', $splString->kebab()->__toString());
    }

    public function testSnake() {
        $splString = new SplString('EasySwoole');
        $this->assertEquals('easy_swoole', $splString->snake()->__toString());
    }

    public function testStudly() {
        $splString = new SplString('easy_swoole');
        $this->assertEquals('EasySwoole', $splString->studly()->__toString());
    }

    public function testCamel() {
        $splString = new SplString('easy_swoole');
        $this->assertEquals('easySwoole', $splString->camel()->__toString());
    }

    public function testReplaceArray() {
        $splString = new SplString('easy_easy_easy');
        $this->assertEquals('as_bs_cs', $splString->replaceArray('easy', ['as', 'bs', 'cs'])->__toString());
    }

    public function testReplaceFirst() {
        $splString = new SplString('easy_easy_easy');
        $this->assertEquals('as_easy_easy', $splString->replaceFirst('easy', 'as')->__toString());
    }

    public function testReplaceLast() {
        $splString = new SplString('easy_easy_easy');
        $this->assertEquals('easy_easy_as', $splString->replaceLast('easy', 'as')->__toString());
    }

    public function testStart() {
        $splString = new SplString('Easyswoole');
        $this->assertEquals('Hello,Easyswoole', $splString->start('Hello,')->__toString());
    }

    public function testAfter() {
        $splString = new SplString('Hello,Easyswoole');
        $this->assertEquals('Easyswoole', $splString->after('Hello,')->__toString());
    }

    public function testBefore() {
        $splString = new SplString('Hello,Easyswoole');
        $this->assertEquals('Hello,', $splString->before('Easyswoole')->__toString());
    }

    public function testEndsWith() {
        $splString = new SplString('Hello,Easyswoole');
        $this->assertTrue($splString->endsWith('Easyswoole'));
    }

    public function testStartsWith() {
        $splString = new SplString('Hello,Easyswoole');
        $this->assertTrue($splString->startsWith('Hello'));
    }
}
