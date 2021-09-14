<?php


namespace EasySwoole\Annotation\Tests;


use EasySwoole\Annotation\Annotation;
use EasySwoole\Annotation\Tests\Tag\TestTagOne;
use EasySwoole\Annotation\Tests\Tag\TestTagTwo;
use PHPUnit\Framework\TestCase;

class AnnotationTest extends TestCase
{
    private $annotation;

    /**
     * @TestTagOne(key="myKey");
     */
    private $property;

    function __construct($name = null, array $data = [], $dataName = '')
    {
        $this->annotation = new Annotation();
        $this->annotation->addParserTag(new TestTagOne());
        $this->annotation->addParserTag(new TestTagTwo());
        parent::__construct($name, $data, $dataName);
    }


    function testProperty()
    {
       $ret =  $this->annotation->getAnnotation((new \ReflectionClass(static::class))->getProperty('property'));
       $this->assertEquals('myKey',$ret['TestTagOne'][0]->key);
    }

}