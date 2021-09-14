<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures;

use EasySwoole\DoctrineAnnotation\Tests\Bar2\Autoload;

class ClassOverwritesTrait
{
    use TraitWithAnnotatedMethod;

    /**
     * @Autoload
     */
    public function traitMethod(): void
    {
    }
}

namespace EasySwoole\DoctrineAnnotation\Tests\Bar2;

/** @Annotation */
class Autoload
{
}
