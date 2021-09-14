<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures;

use EasySwoole\DoctrineAnnotation\Tests\Fixtures\Annotation\Route;
use EasySwoole\DoctrineAnnotation\Tests\Fixtures\Annotation\Template;

/**
 * @Route("/someprefix")
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class ControllerWithParentClass extends AbstractController
{
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
