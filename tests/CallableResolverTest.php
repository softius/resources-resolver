<?php

namespace Softius\Resolver\Test;

use League\Container\Container;
use Softius\ResourcesResolver\CallableResolver;
use Softius\ResourcesResolver\DefaultCallableStrategy;

class CallableResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testResolvesFromFull()
    {
        $resolver = new CallableResolver();
        $callable = $resolver->resolve('Greeting::action');
        $this->assertEquals($callable, ['Greeting', 'action']);
        $this->assertTrue(is_callable($callable, true));
    }

    public function testResolvesSelf()
    {
        $resolver = new CallableResolver();
        $callable = $resolver->resolve('::action');
        $this->assertEquals($callable, [__CLASS__, 'action']);
        $this->assertTrue(is_callable($callable, true));

        $callable = $resolver->resolve('self::action');
        $this->assertEquals($callable, [__CLASS__, 'action']);
        $this->assertTrue(is_callable($callable, true));
    }

    public function testResolvesFromParent()
    {
        $resolver = new CallableResolver();
        $callable = $resolver->resolve('parent::action');
        $this->assertEquals($callable, [get_parent_class(__CLASS__), 'action']);
        $this->assertTrue(is_callable($callable, true));
    }

    public function testThrowsExceptionForInvalidPatterns()
    {
        $resolver = new CallableResolver;
        $this->setExpectedException('Exception');
        $resolver->resolve('!this!fails!');
    }

    public function testConstructorArguments()
    {
        $container = new Container();
        $container->add('Resource/SomeClass', 'Softius\ResourcesResolver\Test\Resource\SomeClass');

        $resolver = new CallableResolver($container, '#');

        $callable = $resolver->resolve('#action');
        $this->assertEquals($callable, [__CLASS__, 'action']);
        $this->assertTrue(is_callable($callable, true));

        $callable = $resolver->resolve('Resource/SomeClass#someStaticMethod');
        $this->assertEquals(get_class($callable[0]), 'Softius\ResourcesResolver\Test\Resource\SomeClass');
        $this->assertTrue(is_callable($callable, true));
    }

    public function testStrictStaticMethods()
    {
        $resolver = new CallableResolver();
        $resolver->setMode(CallableResolver::MODE_STRICT);

        $callable = $resolver->resolve('Softius\ResourcesResolver\Test\Resource\SomeClass::someStaticMethod');
        $this->assertTrue(is_callable($callable));
        $this->assertEquals($callable, ['Softius\ResourcesResolver\Test\Resource\SomeClass', 'someStaticMethod']);
    }

    public function testStrictNonStaticMethods()
    {
        $container = new Container();
        $container->add('SomeClass', 'Softius\ResourcesResolver\Test\Resource\SomeClass');

        $resolver = new CallableResolver($container);
        $resolver->setMode(CallableResolver::MODE_STRICT);

        $callable = $resolver->resolve('SomeClass::someStaticMethod');
        $this->assertTrue(is_callable($callable));
    }

    public function testLazyLoadStaticMethods()
    {
        $resolver = new CallableResolver();
        $resolver->setMode(CallableResolver::MODE_LAZYLOAD);
        $lazy_load = $resolver->resolve('Greeting::action');
        $callable = $lazy_load();
        $this->assertEquals($callable, ['Greeting', 'action']);
        $this->assertTrue(is_callable($callable, true));
    }

    public function testStrictLazyLoadStaticMethods()
    {
        $resolver = new CallableResolver();
        $resolver->setMode(CallableResolver::MODE_LAZYLOAD | CallableResolver::MODE_STRICT);

        $lazy_load = $resolver->resolve('Softius\ResourcesResolver\Test\Resource\SomeClass::someStaticMethod');
        $callable = $lazy_load();
        $this->assertTrue(is_callable($callable));
        $this->assertEquals($callable, ['Softius\ResourcesResolver\Test\Resource\SomeClass', 'someStaticMethod']);
    }

    public function testThrowsExceptionForInvalidMethods()
    {
        $resolver = new CallableResolver;
        $resolver->setMode(CallableResolver::MODE_STRICT);
        $this->setExpectedException('Exception');
        $resolver->resolve('Softius\ResourcesResolver\Test\Resource\SomeClass::doesNotExist');
    }
}