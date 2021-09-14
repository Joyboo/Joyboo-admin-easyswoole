<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures\Annotation;

/** @Annotation */
class AnnotWithDefaultValue
{
    /** @var string */
    public $foo = 'bar';
}
