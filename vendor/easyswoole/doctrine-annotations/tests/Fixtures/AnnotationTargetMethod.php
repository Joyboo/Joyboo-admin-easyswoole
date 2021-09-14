<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class AnnotationTargetMethod
{
    /** @var mixed */
    public $data;
    /** @var mixed */
    public $name;
    /** @var mixed */
    public $target;
}
