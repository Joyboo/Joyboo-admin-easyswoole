<?php

namespace EasySwoole\DoctrineAnnotation\Tests\Ticket;

use EasySwoole\DoctrineAnnotation\AnnotationReader;
use EasySwoole\DoctrineAnnotation\DocParser;
use EasySwoole\DoctrineAnnotation\SimpleAnnotationReader;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

use function array_combine;
use function array_map;

//Some class named Entity in the global namespace
include __DIR__ . '/DCOM58Entity.php';

/**
 * @group DCOM58
 */
class DCOM58Test extends TestCase
{
    public function testIssue(): void
    {
        $reader = new AnnotationReader();
        $result = $reader->getClassAnnotations(new ReflectionClass(__NAMESPACE__ . '\MappedClass'));

        $classAnnotations = array_combine(
            array_map('get_class', $result),
            $result
        );

        self::assertArrayNotHasKey('', $classAnnotations, 'Class "xxx" is not a valid entity or mapped super class.');
    }

    public function testIssueGlobalNamespace(): void
    {
        $docblock = '@Entity';
        $parser   = new DocParser();
        $parser->setImports(['__NAMESPACE__' => 'EasySwoole\DoctrineAnnotation\Tests\Ticket\Doctrine\ORM\Mapping']);

        $annots = $parser->parse($docblock);

        self::assertCount(1, $annots);
        self::assertInstanceOf(Doctrine\ORM\Mapping\Entity::class, $annots[0]);
    }

    public function testIssueNamespaces(): void
    {
        $docblock = '@Entity';
        $parser   = new DocParser();
        $parser->addNamespace('EasySwoole\DoctrineAnnotation\Tests\Ticket\Doctrine\ORM');

        $annots = $parser->parse($docblock);

        self::assertCount(1, $annots);
        self::assertInstanceOf(Doctrine\ORM\Entity::class, $annots[0]);
    }

    public function testIssueMultipleNamespaces(): void
    {
        $docblock = '@Entity';
        $parser   = new DocParser();
        $parser->addNamespace('EasySwoole\DoctrineAnnotation\Tests\Ticket\Doctrine\ORM\Mapping');
        $parser->addNamespace('EasySwoole\DoctrineAnnotation\Tests\Ticket\Doctrine\ORM');

        $annots = $parser->parse($docblock);

        self::assertCount(1, $annots);
        self::assertInstanceOf(Doctrine\ORM\Mapping\Entity::class, $annots[0]);
    }

    public function testIssueWithNamespacesOrImports(): void
    {
        $docblock = '@Entity';
        $parser   = new DocParser();
        $annots   = $parser->parse($docblock);

        self::assertCount(1, $annots);
        self::assertInstanceOf(\Entity::class, $annots[0]);
    }

    public function testIssueSimpleAnnotationReader(): void
    {
        $reader = new SimpleAnnotationReader();
        $reader->addNamespace('EasySwoole\DoctrineAnnotation\Tests\Ticket\Doctrine\ORM\Mapping');
        $annots = $reader->getClassAnnotations(new ReflectionClass(__NAMESPACE__ . '\MappedClass'));

        self::assertCount(1, $annots);
        self::assertInstanceOf(Doctrine\ORM\Mapping\Entity::class, $annots[0]);
    }
}

/**
 * @Entity
 */
class MappedClass
{
}

namespace EasySwoole\DoctrineAnnotation\Tests\Ticket\Doctrine\ORM\Mapping;

/**
 * @Annotation
 */
class Entity
{
}

namespace EasySwoole\DoctrineAnnotation\Tests\Ticket\Doctrine\ORM;

/**
 * @Annotation
 */
class Entity
{
}
