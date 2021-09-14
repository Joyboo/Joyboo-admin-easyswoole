<?php


namespace EasySwoole\DoctrineAnnotation\Tests;


use EasySwoole\DoctrineAnnotation\AnnotationReader;
use PHPUnit\Framework\TestCase;
use EasySwoole\DoctrineAnnotation\Tests\Tag\PropertyTag;

class JsonArrayTest extends TestCase
{

    /**
     * @PropertyTag(input={"code":2,"result":[{"name":1}]})
     */
    private $jsonArray;

    private $ref;
    private $reader;

    function __construct($name = null, array $data = [], $dataName = '')
    {
        $t = new PropertyTag();
        $this->ref = new \ReflectionClass(static::class);
        $this->reader = new AnnotationReader();
        parent::__construct($name, $data, $dataName);
    }

    function testJsonArray()
    {
        $ret = $this->reader->getPropertyAnnotations($this->ref->getProperty('jsonArray'));
        $this->assertEquals(['code'=>2,'result'=>[['name'=>1]]],$ret[0]->input);
    }
}