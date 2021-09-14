<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures;

use EasySwoole\DoctrineAnnotation\Tests\Fixtures\Annotation\Route;
use EasySwoole\DoctrineAnnotation\Tests\Fixtures\Traits\TraitThatUsesAnotherTrait;

/**
 * @Route("/someprefix")
 */
class ClassThatUsesTraitThatUsesAnotherTrait
{
    use TraitThatUsesAnotherTrait;
}
