<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures;

/**
 * @Annotation
 * @Target("ALL")
 */
final class AnnotationEnum
{
    public const ONE   = 'ONE';
    public const TWO   = 'TWO';
    public const THREE = 'THREE';

    /**
     * @var mixed
     * @Enum({"ONE","TWO","THREE"})
     */
    public $value;
}
