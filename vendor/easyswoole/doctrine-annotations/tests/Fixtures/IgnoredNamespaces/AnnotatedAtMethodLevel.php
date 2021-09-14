<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures\IgnoredNamespaces;

class AnnotatedAtMethodLevel
{
    /**
     * @SomeMethodAnnotationNamespace\Subnamespace\Name
     */
    public function test(): void
    {
    }
}
