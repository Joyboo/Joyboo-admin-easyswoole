<?php


namespace EasySwoole\DoctrineAnnotation\Tests;


use EasySwoole\DoctrineAnnotation\AnnotationReader;
use EasySwoole\DoctrineAnnotation\Tests\Tag\NonePropertyTag;
use EasySwoole\DoctrineAnnotation\Tests\Tag\PropertyTag;
use PHPUnit\Framework\TestCase;


class PlainTextTest extends TestCase
{
    /**
     * @PropertyTag(input={"code":2})
     * @PropertyTag(input=r"{"code":2,"result":[{"name":1}]}")
     */
    private $property1;

    /**
     * @NonePropertyTag({"code":2})
     * @NonePropertyTag(r"{"code":2,"result":[{"name":1}]}")
     */
    private $property2;


    private $ref;
    private $reader;

    function __construct($name = null, array $data = [], $dataName = '')
    {
        new NonePropertyTag();
        new PropertyTag();
        $this->ref = new \ReflectionClass(static::class);
        $this->reader = new AnnotationReader();
        parent::__construct($name, $data, $dataName);
    }

    function testProperty()
    {
        $ret = $this->reader->getPropertyAnnotations($this->ref->getProperty('property1'));
        $this->assertIsArray($ret);
        $this->assertEquals(2,count($ret));
        $this->assertEquals(["code"=>2],$ret[0]->input);
        $this->assertEquals('{"code":2,"result":[{"name":1}]}',$ret[1]->input);
    }

    function testNoneProperty()
    {
        $ret = $this->reader->getPropertyAnnotations($this->ref->getProperty('property2'));
        $this->assertIsArray($ret);
        $this->assertEquals(2,count($ret));
        $this->assertEquals(["code"=>2],$ret[0]->value);
        $this->assertEquals('{"code":2,"result":[{"name":1}]}',$ret[1]->value);
    }

}