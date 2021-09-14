<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures;

use EasySwoole\DoctrineAnnotation\Tests\Fixtures\Annotation\Route;
use EasySwoole\DoctrineAnnotation\Tests\Fixtures\Annotation\Template;
use EasySwoole\DoctrineAnnotation\Tests\Fixtures\Traits\SecretRouteTrait;

/**
 * @Route("/someprefix")
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class ControllerWithTrait
{
    use SecretRouteTrait;

    /**
     * @return mixed[]
     *
     * @Route("/", name="_demo")
     * @Template()
     */
    public function indexAction(): array
    {
        return [];
    }
}
