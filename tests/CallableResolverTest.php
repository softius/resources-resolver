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

    public function testSafeStaticMethods()
    {
        $resolver = new CallableResolver();

        $callable = $resolver->resolveSafe('Softius\ResourcesResolver\Test\Resource\SomeClass::someStaticMethod');
        $this->assertTrue(is_callable($callable));
        $this->assertEquals($callable, ['Softius\ResourcesResolver\Test\Resource\SomeClass', 'someStaticMethod']);
    }

    public function testSafeNonStaticMethods()
    {
        $container = new Container();
        $container->add('SomeClass', 'Softius\ResourcesResolver\Test\Resource\SomeClass');

        $resolver = new CallableResolver($container);

        $callable = $resolver->resolveSafe('SomeClass::someStaticMethod');
        $this->assertTrue(is_callable($callable));
    }

    public function testThrowsExceptionForInvalidMethods()
    {
        $resolver = new CallableResolver;
        $this->setExpectedException('Exception');
        $resolver->resolveSafe('Softius\ResourcesResolver\Test\Resource\SomeClass::doesNotExist');
    }
}