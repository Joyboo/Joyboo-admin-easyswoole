<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;


use EasySwoole\Annotation\AbstractAnnotationTag;

/**
 * Class Controller
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class Controller extends AbstractAnnotationTag
{

    /** @var string */
    public $prefix;

    public function tagName(): string
    {
        return 'Controller';
    }
}
