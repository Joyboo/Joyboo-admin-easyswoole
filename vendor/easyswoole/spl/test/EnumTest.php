<?php
/**
 * @CreateTime:   2019/9/9 下午05:47
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2019) Easyswoole all rights reserved
 * @Description:  SplEnum 单元测试
 */
namespace EasySwoole\Spl\Test;

use EasySwoole\Spl\Test\Enum\Month;
use PHPUnit\Framework\TestCase;

class EnumTest extends TestCase {

    public function testConstruct() {
        $month = new Month(1);
        $this->assertEquals(
            'JANUARY',
            $month->getName()
        );
    }

    public function testGetName() {
        $month = new Month(1);
        $this->assertEquals(
            'JANUARY',
            $month->getName()
        );
    }

    public function testGetValue() {
        $month = new Month(1);
        $this->assertEquals(
            1,
            $month->getValue()
        );
    }

    public function testIsValidName() {
        $this->assertTrue(
            Month::isValidName('JANUARY')
        );
    }

    public function testIsValidValue() {
        $this->assertEquals(
            'JANUARY',
            Month::isValidValue(1)
        );
    }

    public function testGetEnumList() {
        $this->assertEquals(
            [
                'JANUARY' => 1,
                'FEBRUARY' => 2,
                'MARCH' => 3,
                'APRIL' => 4,
                'MAY' => 5,
                'JUNE' => 6,
                'JULY' => 7,
                'AUGUST' => 8,
                'SEPTEMBER' => 9,
                'OCTOBER' => 10,
                'NOVEMBER' => 11,
                'DECEMBER' => 12,
            ],
            Month::getEnumList()
        );
    }

}