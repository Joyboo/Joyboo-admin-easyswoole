<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures;

class ClassWithPhpCsSuppressAnnotation
{
    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingAnyTypeHint
     */
    public function foo($parameterWithoutTypehint): void
    {
    }
}
