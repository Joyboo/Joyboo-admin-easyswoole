<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures;

use EasySwoole\DoctrineAnnotation\Tests\Fixtures\AnnotationTargetAnnotation;
use EasySwoole\DoctrineAnnotation\Tests\Fixtures\AnnotationTargetClass;

/**
 * @AnnotationTargetClass("Some data")
 */
class ClassWithInvalidAnnotationTargetAtProperty
{
    /**
     * @var mixed
     * @AnnotationTargetClass("Bar")
     */
    public $foo;


    /**
     * @var mixed
     * @AnnotationTargetAnnotation("Foo")
     */
    public $bar;
}
