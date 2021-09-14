<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures;

use EasySwoole\DoctrineAnnotation\Tests\Fixtures\AnnotationEnum;

class ClassWithAnnotationEnum
{
    /**
     * @var mixed
     * @AnnotationEnum(AnnotationEnum::ONE)
     */
    public $foo;

    /**
     * @AnnotationEnum("TWO")
     */
    public function bar(): void
    {
    }

    /**
     * @var mixed
     * @AnnotationEnum("FOUR")
     */
    public $invalidProperty;

    /**
     * @AnnotationEnum(5)
     */
    public function invalidMethod(): void
    {
    }
}
