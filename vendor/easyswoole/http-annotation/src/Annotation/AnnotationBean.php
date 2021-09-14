<?php


namespace EasySwoole\HttpAnnotation\Annotation;


use EasySwoole\Annotation\AbstractAnnotationTag;
use EasySwoole\HttpAnnotation\AnnotationTag\Param;

class AnnotationBean
{
    protected $__otherTags = [];

    public function addAnnotationTag(AbstractAnnotationTag $annotationTag)
    {
        $propertyName = lcfirst($annotationTag->tagName());
        if (property_exists($this, $propertyName)) {
            if (is_array($this->{$propertyName})) {
                if ($annotationTag instanceof Param) {
                    $annotationTag->name && $this->{$propertyName}[$annotationTag->name] = $annotationTag;
                } else {
                    $this->{$propertyName}[] = $annotationTag;
                }
            } else {
                $this->{$propertyName} = $annotationTag;
            }
        } else {
            $this->__otherTags[$annotationTag->tagName()][] = $annotationTag;
        }
    }

    function getOtherTags(): array
    {
        return $this->__otherTags;
    }
}
