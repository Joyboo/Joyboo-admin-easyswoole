<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures;

use EasySwoole\DoctrineAnnotation\Tests\Fixtures\AnnotationTargetAll;
use EasySwoole\DoctrineAnnotation\Tests\Fixtures\AnnotationTargetAnnotation;
use EasySwoole\DoctrineAnnotation\Tests\Fixtures\AnnotationWithVarType;

class ClassWithAnnotationWithVarType
{
    /**
     * @var mixed
     * @AnnotationWithVarType(string = "String Value")
     */
    public $foo;

    /**
     * @AnnotationWithVarType(annotation = @AnnotationTargetAll)
     */
    public function bar(): void
    {
    }

    /**
     * @var mixed
     * @AnnotationWithVarType(string = 123)
     */
    public $invalidProperty;

    /**
     * @AnnotationWithVarType(annotation = @AnnotationTargetAnnotation)
     */
    public function invalidMethod(): void
    {
    }
}
