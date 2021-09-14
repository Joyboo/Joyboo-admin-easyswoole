<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class AnnotationTargetClass
{
    /** @var mixed */
    public $data;
    /** @var mixed */
    public $name;
    /** @var mixed */
    public $target;
}
