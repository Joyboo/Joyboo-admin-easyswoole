<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures;

use EasySwoole\DoctrineAnnotation\Tests\Fixtures\AnnotationTargetPropertyMethod;

/**
 * @AnnotationTargetPropertyMethod("Some data")
 */
class ClassWithInvalidAnnotationTargetAtClass
{
    /**
     * @var mixed
     * @AnnotationTargetPropertyMethod("Bar")
     */
    public $foo;
}
