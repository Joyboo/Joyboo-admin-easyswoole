<?php
namespace EasySwoole\HttpAnnotation\AnnotationTag;



/**
 * Class ApiResponseParam
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class ApiSuccessParam extends Param
{
    public function tagName(): string
    {
        return 'ApiSuccessParam';
    }
}