<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;


use EasySwoole\Annotation\AbstractAnnotationTag;

/**
 * Class Di
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class Di extends AbstractAnnotationTag
{

    /**
     * @var string
     */
    public $key;

    public function tagName(): string
    {
        return 'Di';
    }

}