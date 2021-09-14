<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures;

use EasySwoole\DoctrineAnnotation\Tests\Fixtures\Annotation\Param;

class ClassWithImportedIgnoredAnnotation
{
    /**
     * @param string $foo
     */
    public function something($foo): void
    {
    }
}
