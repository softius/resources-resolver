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
     * @param string $class
     * @return string
     */
    protected function fillClass($class)
    {
        // Use backtrace to find the calling Class
        if (empty($class) || $class === 'parent' || $class === 'self') {
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
            $class = ($class === 'parent') ? get_parent_class($trace[2]['class']) : $trace[2]['class'];
        }

        if ($this->container !== null && $this->container->has($class)) {
            $class = $this->container->get($class);
        }

        return $class;
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
        $class = $this->fillClass($class);
        $method = substr($in, $pos + strlen($this->method_separator));

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
