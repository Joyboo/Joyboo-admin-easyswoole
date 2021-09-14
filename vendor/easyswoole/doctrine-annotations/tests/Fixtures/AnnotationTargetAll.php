<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures;

/**
 * @Annotation
 * @Target("ALL")
 */
class AnnotationTargetAll
{
    /** @var mixed */
    public $data;
    /** @var mixed */
    public $name;
    /** @var mixed */
    public $target;
}
