<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;

use EasySwoole\Annotation\AbstractAnnotationTag;

/**
 * Class Context
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class Context extends AbstractAnnotationTag
{
    /**
     * @var string
     */
    public $key;

    public function tagName(): string
    {
        return 'Context';
    }
}