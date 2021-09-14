<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures;

/**
 * @ignoreAnnotation("IgnoreAnnotationClass")
 */
class ClassWithIgnoreAnnotation
{
    /**
     * @var mixed[]
     * @IgnoreAnnotationClass
     */
    public $foo;
}
