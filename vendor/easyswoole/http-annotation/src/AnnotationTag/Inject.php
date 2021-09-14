<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;

use EasySwoole\Annotation\AbstractAnnotationTag;
use EasySwoole\HttpAnnotation\Exception\Annotation\InvalidTag;

/**
 * Class Inject
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class Inject extends AbstractAnnotationTag
{
    public $className;

    public $args = [];

    public function tagName(): string
    {
        return 'Inject';
    }

    public function __onParser()
    {
        if (empty($this->className)) {
            throw new InvalidTag("Inject for @Inject is require");
        }

        if (!is_string($this->className)) {
            throw new InvalidTag('className for @Inject must to be string');
        }

        if (!class_exists($this->className)) {
            throw new InvalidTag('the class specified by @Inject does not exist');
        }
    }
}