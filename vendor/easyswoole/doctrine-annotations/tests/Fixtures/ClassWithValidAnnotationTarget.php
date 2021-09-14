<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures;

use EasySwoole\DoctrineAnnotation\Tests\Fixtures\AnnotationTargetAll;
use EasySwoole\DoctrineAnnotation\Tests\Fixtures\AnnotationTargetClass;
use EasySwoole\DoctrineAnnotation\Tests\Fixtures\AnnotationTargetPropertyMethod;

/**
 * @AnnotationTargetClass("Some data")
 */
class ClassWithValidAnnotationTarget
{
    /** @AnnotationTargetPropertyMethod("Some data") */
    public $foo;


    /** @AnnotationTargetAll("Some data",name="Some name") */
    public $name;

    /**
     * @AnnotationTargetPropertyMethod("Some data",name="Some name")
     */
    public function someFunction(): void
    {
    }

    /** @AnnotationTargetAll(@AnnotationTargetAnnotation) */
    public $nested;
}
