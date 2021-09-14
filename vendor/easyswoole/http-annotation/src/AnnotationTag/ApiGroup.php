<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;


use EasySwoole\Annotation\AbstractAnnotationTag;
use EasySwoole\HttpAnnotation\Exception\Annotation\InvalidTag;

/**
 * Class ApiGroup
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class ApiGroup extends AbstractAnnotationTag
{
    public $groupName;

    public function tagName(): string
    {
        return 'ApiGroup';
    }

    function __onParser()
    {
        if(empty($this->groupName)){
            throw new InvalidTag("groupName for ApiGroup tag is require");
        }
    }
}