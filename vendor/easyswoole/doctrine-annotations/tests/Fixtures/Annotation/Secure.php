<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures\Annotation;

use function is_string;

/** @Annotation */
class Secure
{
    /** @var mixed */
    public $roles;

    /**
     * @param mixed[] $values
     */
    public function __construct(array $values)
    {
        if (is_string($values['value'])) {
            $values['value'] = [$values['value']];
        }

        $this->roles = $values['value'];
    }
}
