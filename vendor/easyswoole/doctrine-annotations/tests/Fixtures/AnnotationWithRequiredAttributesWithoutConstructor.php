<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures;

/**
 * @Annotation
 * @Target("ALL")
 */
final class AnnotationWithRequiredAttributesWithoutConstructor
{
    /**
     * @Required
     * @var string
     */
    public $value;

    /**
     * @Required
     * @var \EasySwoole\DoctrineAnnotation\Tests\Fixtures\AnnotationTargetAnnotation
     */
    public $annot;
}
