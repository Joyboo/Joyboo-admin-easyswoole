<?php

namespace EasySwoole\HttpAnnotation\AnnotationTag;

/**
 * Class ApiGroupAuth
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class ApiGroupAuth extends Param
{
    /**
     * @var array
     */
    public $ignoreAction = [];

    public function tagName(): string
    {
        return 'ApiGroupAuth';
    }
}