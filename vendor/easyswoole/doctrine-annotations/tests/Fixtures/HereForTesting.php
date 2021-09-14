<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures;

use EasySwoole\DoctrineAnnotation\Tests\Fixtures\Annotation\Secure;

interface HereForTesting
{
    /**
     * @Secure
     */
    public function foo();
}
