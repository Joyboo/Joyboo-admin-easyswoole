<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;

use EasySwoole\Annotation\AbstractAnnotationTag;
/**
 * Class InjectParamsContext
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class InjectParamsContext extends AbstractAnnotationTag
{
    public $key = 'INJECT_PARAMS';

    public $onlyParamTag = true;

    /**
     * @var bool
     */
    public $filterNull = false;

    /**
     * @var bool
     */
    public $filterEmpty = false;

    public function tagName(): string
    {
        return 'InjectParamsContext';
    }
}