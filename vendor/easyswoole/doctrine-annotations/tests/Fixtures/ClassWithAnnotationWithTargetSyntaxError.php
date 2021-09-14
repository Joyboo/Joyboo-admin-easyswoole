<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures;

use EasySwoole\DoctrineAnnotation\Tests\Fixtures\AnnotationWithTargetSyntaxError;

/**
 * @AnnotationWithTargetSyntaxError()
 */
class ClassWithAnnotationWithTargetSyntaxError
{
    /**
     * @var mixed
     * @AnnotationWithTargetSyntaxError()
     */
    public $foo;

    /**
     * @AnnotationWithTargetSyntaxError()
     */
    public function bar(): void
    {
    }
}
