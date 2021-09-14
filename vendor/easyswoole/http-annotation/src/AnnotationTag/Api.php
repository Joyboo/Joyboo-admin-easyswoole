<?php


namespace EasySwoole\HttpAnnotation\AnnotationTag;


use EasySwoole\Annotation\AbstractAnnotationTag;
use EasySwoole\HttpAnnotation\Exception\Annotation\InvalidTag;

/**
 * Class Api
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class Api extends AbstractAnnotationTag
{
    /**
     * @var string
     */
    public $path;
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var bool
     */
    public $deprecated;
    /**
     * @var string
     */
    public $version = '1.0.0';

    /**
     * @var bool
     */
    public $ignorePrefix = false;

    public function tagName(): string
    {
        return  'Api';
    }

    function __onParser()
    {
        if(empty($this->name)){
            throw new InvalidTag("name for Api tag is require");
        }
    }
}
