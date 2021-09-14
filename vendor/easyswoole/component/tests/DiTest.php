<?php

namespace EasySwoole\Component\Tests;

use EasySwoole\Component\Di;
use EasySwoole\Component\Tests\Lib\Get;
use PHPUnit\Framework\TestCase;
use EasySwoole\Component\Tests\Lib\Bar;
use EasySwoole\Component\Tests\Lib\Foo;

class DiTest extends TestCase
{
    protected function setUp(): void
    {
        Di::getInstance()->clear();
    }

    public function testSetAndGet()
    {
        // string
        Di::getInstance()->set('string', 'string');
        $this->assertEquals(Di::getInstance()->get('string'), 'string');

        // callback
        Di::getInstance()->set('callback', function () {
            return 'callback';
        });
        $this->assertIsCallable(Di::getInstance()->get('callback'));
        $this->assertEquals('callback', call_user_func(Di::getInstance()->get('callback')));

        // object
        Di::getInstance()->set('object', new class {
            public $foo = 1;

            public function bar()
            {
                return 'bar';
            }
        });
        $this->assertIsObject(Di::getInstance()->get('object'));
        $this->assertEquals('bar', call_user_func([Di::getInstance()->get('object'), 'bar']));
        Di::getInstance()->get('object')->foo = 2;
        $this->assertEquals(2, Di::getInstance()->get('object')->foo);

        // ref di
        Di::getInstance()->set(Bar::class, Bar::class);
        Di::getInstance()->set(Foo::class, Foo::class);
        $this->assertEquals(Di::getInstance()->get(Foo::class)->bar->bar, 'bar');

        Di::getInstance()->set(Get::class, Get::class);
        Di::getInstance()->set('foo', Foo::class);
        $this->assertInstanceOf(Foo::class, Di::getInstance()->get(Get::class)->foo);
        Di::getInstance()->delete('foo');
        Di::getInstance()->set(Get::class, Get::class);
        $this->assertEquals(1, Di::getInstance()->get(Get::class)->foo);
    }

    public function testAlias()
    {
        Di::getInstance()->set('string', 'value');
        Di::getInstance()->alias('string-alias', 'string');
        $this->assertEquals('value', Di::getInstance()->get('string-alias'));
        Di::getInstance()->deleteAlias('string-alias');
        $this->assertEquals(null, Di::getInstance()->get('string-alias'));
    }

    public function testDelete()
    {
        Di::getInstance()->set('string', 'value');
        Di::getInstance()->set('string1', 'value');
        $this->assertEquals('value', Di::getInstance()->get('string'));
        $this->assertEquals('value', Di::getInstance()->get('string1'));
        Di::getInstance()->delete('string');
        $this->assertEquals(null, Di::getInstance()->get('string'));
        $this->assertEquals('value', Di::getInstance()->get('string1'));
    }

    public function testClear()
    {
        Di::getInstance()->set('string', 'value');
        Di::getInstance()->set('string1', 'value');
        $this->assertEquals('value', Di::getInstance()->get('string'));
        $this->assertEquals('value', Di::getInstance()->get('string1'));
        Di::getInstance()->clear();
        $this->assertEquals(null, Di::getInstance()->get('string'));
        $this->assertEquals(null, Di::getInstance()->get('string1'));
    }

    public function testOnMissKey()
    {
        Di::getInstance()->setOnKeyMiss(function ($key) {
            return "{$key} miss";
        });

        $this->assertEquals('string miss', Di::getInstance()->get('string'));
    }
}
