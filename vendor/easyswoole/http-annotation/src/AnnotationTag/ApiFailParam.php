<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;

/**
 * Class ApiFailParam
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class ApiFailParam extends Param
{
    public function tagName(): string
    {
        return 'ApiFailParam';
    }
}