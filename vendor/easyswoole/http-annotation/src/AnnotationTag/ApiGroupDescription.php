<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;

/**
 * Class ApiGroupDescription
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class ApiGroupDescription extends ApiDescription
{
    public function tagName(): string
    {
       return 'ApiGroupDescription';
    }
}