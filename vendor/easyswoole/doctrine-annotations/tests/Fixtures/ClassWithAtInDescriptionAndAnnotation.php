<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures;

use EasySwoole\DoctrineAnnotation\Tests\Fixtures\AnnotationTargetPropertyMethod;

class ClassWithAtInDescriptionAndAnnotation
{
    /**
     * Lala
     *
     * {
     *     "email": "foo@example.com",
     *     "email2": "123@example.com",
     *     "email3": "@example.com"
     * }
     *
     * @AnnotationTargetPropertyMethod("Bar")
     * @var mixed
     */
    public $foo;

    /**
     * Lala
     *
     * {
     *     "email": "foo@example.com",
     *     "email2": "123@example.com",
     *     "email3": "@example.com"
     * }
     *
     * @AnnotationTargetPropertyMethod("Bar")
     * @var mixed
     */
    public $bar;
}
