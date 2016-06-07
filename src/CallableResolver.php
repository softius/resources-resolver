<?php

namespace Softius\ResourcesResolver;

use Interop\Container\ContainerInterface;

/**
 * Class CallableResolver.
 */
class CallableResolver implements ResolvableInterface
{
    const DEFAULT_METHOD_SEPARATOR = '::';

    const MODE_STRICT = 0b0001;
    const MODE_LAZYLOAD = 0b0010;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var null|string
     */
    private $method_separator;

    /**
     * @var int
     */
    private $modes;

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
        $this->modes = 0;
    }

    /**
     * @param string $class
     * @return string
     */
    protected function fillClass($class)
    {
        // Use backtrace to find the calling Class
        if (empty($class) || $class === 'parent' || $class === 'self') {
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 4);
            $class = ($class === 'parent') ? get_parent_class($trace[3]['class']) : $trace[3]['class'];
        }

        if ($this->container !== null && $this->container->has($class)) {
            $class = $this->container->get($class);
        }

        return $class;
    }

    /**
     * @param int $mode
     */
    public function setMode($mode)
    {
        $this->modes = $modes;
    }

    /**
     * @param int $mode
     * @return boolean
     */
    public function isMode($mode)
    {
        return ($this->modes & $mode);
    }

    /**
     * @param string $in
     * @return array|\Closure
     * @throws \Exception
     */
    public function resolve($in)
    {
        if ($this->isMode(self::MODE_STRICT) && $this->isMode(self::MODE_LAZYLOAD)) {
            return function() use ($in) {
                return $this->resolveStrict($in);
            };
        } elseif ($this->isMode(self::MODE_STRICT)) {
            return $this->resolveStrict($in);
        } elseif ($this->isMode(self::MODE_LAZYLOAD)) {
            return function() use ($in) {
                return $this->resolveCallable($in);
            };
        } else {
            return $this->resolveCallable($in);
        }
    }

    /**
     * @param string $in
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function resolveCallable($in)
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
    protected function resolveStrict($in)
    {
        $callable = $this->resolveCallable($in);
        if (is_callable($callable)) {
            return $callable;
        }

        throw new \Exception(sprintf('Could not resolve %s to a callable', $in));
    }
}
