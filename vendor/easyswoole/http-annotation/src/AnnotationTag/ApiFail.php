<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;


use EasySwoole\Annotation\AbstractAnnotationTag;

/**
 * Class ApiFail
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class ApiFail extends ApiDescription
{
    public function tagName(): string
    {
        return 'ApiFail';
    }
}