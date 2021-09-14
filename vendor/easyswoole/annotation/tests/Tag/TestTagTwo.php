<?php


namespace EasySwoole\Annotation\Tests\Tag;

use EasySwoole\Annotation\AbstractAnnotationTag;

/**
 * Class TestTagTwo
 * @package EasySwoole\Annotation\Tests\Tag
 * @Annotation
 */
class TestTagTwo extends AbstractAnnotationTag
{
    public $value;

    public function tagName(): string
    {
        return 'TestTagTwo';
    }
}