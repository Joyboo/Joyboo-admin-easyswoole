<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures;

use EasySwoole\DoctrineAnnotation\Tests\Fixtures\Annotation\Route;
use EasySwoole\DoctrineAnnotation\Tests\Fixtures\Traits\TraitThatUsesAnotherTrait;

class ClassThatUsesTraitThatUsesAnotherTraitWithMethods
{
    use TraitThatUsesAnotherTrait;

    /**
     * @Route("/someprefix")
     */
    public function method1(): void
    {
    }

    /**
     * @Route("/someotherprefix")
     */
    public function method2(): void
    {
    }
}
