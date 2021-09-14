<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures;

/**
 * The leading \ is intentional to ensure that code using
 * this use statement format works see issue #115/PR #120
 */
use \EasySwoole\DoctrineAnnotation\Tests\Fixtures\Annotation as Annotations;

/**
 * @Annotations\SingleUseAnnotation
 */
class ClassWithFullPathUseStatement
{
}
