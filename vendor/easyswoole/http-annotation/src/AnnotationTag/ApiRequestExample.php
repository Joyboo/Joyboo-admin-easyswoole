<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;


/**
 * Class ApiRequestExample
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class ApiRequestExample extends ApiDescription
{
    public function tagName(): string
    {
        return 'ApiRequestExample';
    }
}