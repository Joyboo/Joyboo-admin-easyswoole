<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;

use EasySwoole\Annotation\AbstractAnnotationTag;

/**
 * Class ApiDescription
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class ApiDescription extends AbstractAnnotationTag
{
    public $type = 'text';//text|file
    public function tagName(): string
    {
        return 'ApiDescription';
    }
}