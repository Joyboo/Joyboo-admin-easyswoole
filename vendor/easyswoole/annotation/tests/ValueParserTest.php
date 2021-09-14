<?php


namespace EasySwoole\Annotation\Tests;


use EasySwoole\Annotation\ValueParser;
use PHPUnit\Framework\TestCase;

class ValueParserTest extends TestCase
{
    function testNormal()
    {
        $str = "int=1";
        $this->assertEquals([
            'int'=>1
        ],ValueParser::parser($str));

        $str = "int=1,int2=2";
        $this->assertEquals([
            'int'=>"1",
            'int2'=>"2"
        ],ValueParser::parser($str));

        $str = "int=1,str='2'";
        $this->assertEquals([
            'int'=>"1",
            'str'=>"'2'"
        ],ValueParser::parser($str));
    }

    function testArray()
    {
        $str = "array={1,2,3}";
        $this->assertEquals([
            'array'=>[1,2,3]
        ],ValueParser::parser($str));

        $str = "array={'1','2','3'}";
        $this->assertEquals([
            'array'=>["'1'","'2'","'3'"]
        ],ValueParser::parser($str));


        $str = 'array={"1","2 , 3"}';
        $this->assertEquals([
            'array'=>["1","2 , 3"]
        ],ValueParser::parser($str));

        $str = "array={1,2,3} ,array2={4,5,6}";
        $this->assertEquals([
            'array'=>[1,2,3],
            'array2'=>[4,5,6]
        ],ValueParser::parser($str));

    }


    function testEval()
    {
        $str = 'time="eval(time() + 30)"';
        $this->assertEquals([
            'time'=>time() + 30,
        ],ValueParser::parser($str));

        $str = 'time="eval(time() + 30)" , time2="eval(time() + 31)';
        $this->assertEquals([
            'time'=>time() + 30,
            'time2'=>time() + 31
        ],ValueParser::parser($str));

        $str = 'list="eval([1,2,3,4])"';
        $this->assertEquals([
            'list'=>[1,2,3,4]
        ],ValueParser::parser($str));
    }

    function testArrayAndEval()
    {
        $str = 'array={1,2,eval(time() + 30)}';
        $this->assertEquals([
            'array'=>[1,2,time() + 30]
        ],ValueParser::parser($str));

        $str = 'array={1,2,eval(time() + 30)},str="222"';
        $this->assertEquals([
            'array'=>[1,2,time() + 30],
            "str"=>'222'
        ],ValueParser::parser($str));

        $str = "array={1,2,3},time=eval(time())";
        $this->assertEquals([
            'array'=>[1,2,3],
            'time'=>time()
        ],ValueParser::parser($str));
    }


    function testStrMulti()
    {
        $str = 'mix={first,{1,2,3},eval(time() + 3)},int=1,strInt="2",arr={1,2,3},b="asdasda",d=abcdefh';
        $this->assertEquals([
            'mix'=>['first',['1','2','3'],time() + 3],
            'int'=>1,
            'strInt'=>'2',
            'arr'=>[1,2,3],
            'b'=>'asdasda',
            'd'=>'abcdefh'
        ],ValueParser::parser($str));
    }
}