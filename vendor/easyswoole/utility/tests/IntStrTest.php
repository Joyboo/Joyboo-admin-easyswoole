<?php


namespace EasySwoole\Utility\Tests;


use EasySwoole\Utility\IntStr;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

/**
 * Class IntStrTest
 * @package EasySwoole\Utility\Tests
 */
class IntStrTest extends TestCase
{

    public function test(){
        for ($i=0;$i<=10000;$i++){
            $str = IntStr::toAlpha($i);
            $this->assertEquals($i,IntStr::toNum($str));
        }
        $int = 489404994190181376;
        $str = IntStr::toAlpha($int);
        $this->assertEquals($int,IntStr::toNum($str));
    }
}
