<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures\IgnoredNamespaces;

use SomePropertyAnnotationNamespace\Subnamespace as SomeAlias;

class AnnotatedWithAlias
{
    /**
     * @var mixed
     * @SomeAlias\Name
     */
    public $property;
}
