<?php

namespace Softius\Resolver\Test;

use Softius\ResourcesResolver\CallableResolver;

class CallableResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testResolvesFromFull()
    {
        $resolver = new CallableResolver;
        $callable = $resolver->resolve('Greeting::action');
        $this->assertEquals($callable, ['Greeting', 'action']);
    }

    public function testResolvesFromPartial()
    {
        $resolver = new CallableResolver;
        $callable = $resolver->resolve('::action');
        $this->assertEquals($callable, [__CLASS__, 'action']);
    }

    public function testThrowsExceptionForInvalidPatterns()
    {
        $resolver = new CallableResolver;
        $this->setExpectedException('Exception');
        $resolver->resolve('!this!fails!');
    }
}