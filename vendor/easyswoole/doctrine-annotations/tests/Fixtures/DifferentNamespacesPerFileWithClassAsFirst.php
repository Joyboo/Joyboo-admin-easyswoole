<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures {

    use EasySwoole\DoctrineAnnotation\Tests\Fixtures\Annotation\Secure;

    class DifferentNamespacesPerFileWithClassAsFirst
    {
    }
}

namespace {

    use EasySwoole\DoctrineAnnotation\Tests\Fixtures\Annotation\Route;

}

namespace EasySwoole\DoctrineAnnotation\Tests\Fixtures\Foo {

    use EasySwoole\DoctrineAnnotation\Tests\Fixtures\Annotation\Template;

}
