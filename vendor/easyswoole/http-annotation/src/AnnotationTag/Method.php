<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;


use EasySwoole\Annotation\AbstractAnnotationTag;
/**
 * Class Method
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class Method extends AbstractAnnotationTag
{
    /**
     * @var array
     */
    public $allow = [];

    public function tagName(): string
    {
        return 'Method';
    }
}