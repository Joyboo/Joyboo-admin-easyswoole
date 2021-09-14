<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;


use EasySwoole\Annotation\AbstractAnnotationTag;

/**
 * Class CircuitBreaker
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class CircuitBreaker extends AbstractAnnotationTag
{
    /** @var float  */
    public $timeout = 3.0;
    /** @var string */
    public $failAction;

    public function tagName(): string
    {
        return 'CircuitBreaker';
    }
}