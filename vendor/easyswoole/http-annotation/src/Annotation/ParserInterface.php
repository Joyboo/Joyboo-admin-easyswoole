<?php


namespace EasySwoole\HttpAnnotation\Annotation;


interface ParserInterface
{
    function parseObject(\ReflectionClass  $reflectionClass):ObjectAnnotation;
}