<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures;

/**
 * @Annotation
 * @Target({ "ANNOTATION" })
 */
final class AnnotationTargetAnnotation
{
    /** @var mixed */
    public $data;
    /** @var mixed */
    public $name;
    /** @var mixed */
    public $target;
}
