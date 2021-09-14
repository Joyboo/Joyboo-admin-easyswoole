<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures;

use EasySwoole\DoctrineAnnotation\Tests\Fixtures\Annotation\Autoload;

trait TraitWithAnnotatedMethod
{
    /**
     * @var mixed
     * @Autoload
     */
    public $traitProperty;

    /**
     * @Autoload
     */
    public function traitMethod(): void
    {
    }
}
