<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;



/**
 * Class ApiAuth
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class ApiAuth extends Param
{
    public function tagName(): string
    {
        return 'ApiAuth';
    }
}