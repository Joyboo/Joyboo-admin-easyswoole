<?php


namespace EasySwoole\Annotation\Tests\Tag;

use EasySwoole\Annotation\AbstractAnnotationTag;

/**
 * Class TestTagOne
 * @package EasySwoole\Annotation\Tests\Tag
 * @Annotation
 */
class TestTagOne extends AbstractAnnotationTag
{
    public $key;

    public function tagName(): string
    {
        return 'TestTagOne';
    }
}