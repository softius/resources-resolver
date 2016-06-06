<?php

namespace Softius\ResourcesResolver;

use Interop\Container\ContainerInterface;

/**
 * Class CallableResolver.
 */
class CallableResolver implements ResolvableInterface
{
    const DEFAULT_METHOD_SEPARATOR = '::';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var null|string
     */
    private $method_separator;

    /**
     * DefaultCallableStrategy constructor.
     *
     * @param \Interop\Container\ContainerInterface $container
     * @param string                                $method_separator
     */
    public function __construct(ContainerInterface $container = null, $method_separator = null)
    {
        $this->container = $container;
        $this->method_separator = ($method_separator === null) ? self::DEFAULT_METHOD_SEPARATOR : $method_separator;
    }

    /**
     * @param string $in
     *
     * @return array
     *
     * @throws \Exception
     */
    public function resolve($in)
    {
        $pos = strrpos($in, $this->method_separator);
        if ($pos === false) {
            throw new \Exception(sprintf('Method separator not found in %s', $in));
        }

        $class = substr($in, 0, $pos);
        $method = substr($in, $pos + strlen($this->method_separator));
        if ($pos === 0 || $class === 'parent' || $class === 'self') {
            // Use backtrace to find the calling Class
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            $class = ($class === 'parent') ? get_parent_class($trace[1]['class']) : $trace[1]['class'];
        }

        if ($this->container !== null && $this->container->has($class)) {
            $class = $this->container->get($class);
        }

        return [$class, $method];
    }

    /**
     * @param $in
     *
     * @return array
     *
     * @throws \Exception
     */
    public function resolveSafe($in)
    {
        $callable = $this->resolve($in);
        if (is_callable($callable)) {
            return $callable;
        }

        throw new \Exception(sprintf('Could not resolve %s to a callable', $in));
    }
}
