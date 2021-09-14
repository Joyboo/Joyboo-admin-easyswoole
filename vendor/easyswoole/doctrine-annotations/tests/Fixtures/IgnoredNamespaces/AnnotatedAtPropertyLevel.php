<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures\IgnoredNamespaces;

class AnnotatedAtPropertyLevel
{
    /**
     * @var mixed
     * @SomePropertyAnnotationNamespace\Subnamespace\Name
     */
    public $property;
}
