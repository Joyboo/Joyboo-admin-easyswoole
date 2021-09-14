<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures;

/**
 * @package EasySwoole\DoctrineAnnotation\Tests\Fixtures
 */
class ClassWithNotRegisteredAnnotationUsed
{
    /**
     * @return bool
     *
     * @notRegisteredCustomAnnotation
     */
    public function methodWithNotRegisteredAnnotation()
    {
        return false;
    }
}
