<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures\Annotation;

/** @Annotation */
class Template
{
    /** @var mixed */
    public $name;

    /**
     * @param mixed[] $values
     */
    public function __construct(array $values)
    {
        $this->name = $values['value'] ?? null;
    }
}
