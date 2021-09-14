<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures\Traits;

use EasySwoole\DoctrineAnnotation\Tests\Fixtures\Annotation\Route;
use EasySwoole\DoctrineAnnotation\Tests\Fixtures\Annotation\Template;

trait SecretRouteTrait
{
    /**
     * @return mixed[]
     *
     * @Route("/secret", name="_secret")
     * @Template()
     */
    public function secretAction(): array
    {
        return [];
    }
}
