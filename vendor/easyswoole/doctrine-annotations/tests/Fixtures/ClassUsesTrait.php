<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures;

use EasySwoole\DoctrineAnnotation\Tests\Bar\Autoload;

class ClassUsesTrait
{
    use TraitWithAnnotatedMethod;

    /**
     * @var mixed
     * @Autoload
     */
    public $aProperty;

    /**
     * @Autoload
     */
    public function someMethod(): void
    {
    }
}

namespace EasySwoole\DoctrineAnnotation\Tests\Bar;

/** @Annotation */
class Autoload
{
}
