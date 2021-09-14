<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures;

// Include a class named Api
require_once __DIR__ . '/Api.php';

use EasySwoole\DoctrineAnnotation\Tests\DummyAnnotationWithIgnoredAnnotation;

/**
 * @DummyAnnotationWithIgnoredAnnotation(dummyValue="hello")
 */
class ClassWithRequire
{
}
