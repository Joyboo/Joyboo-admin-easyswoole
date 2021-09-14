<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures\Annotation;

/** @Annotation */
class Route
{
    /** @var string @Required */
    public $pattern;
    /** @var mixed */
    public $name;
}
