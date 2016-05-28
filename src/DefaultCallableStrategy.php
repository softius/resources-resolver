<?php

namespace Softius\ResourcesResolver;

/**
 * Class DefaultCallableStrategy
 * @package Softius\ResourcesResolver
 */
class DefaultCallableStrategy implements ResolvableInterface
{
	const DEFAULT_NAMESPACE_SEPARATOR = '\\';
	const DEFAULT_METHOD_SEPARATOR = '::';

	/**
	 * @var null|string
     */

	private $namespace_separator;
	/**
	 * @var null|string
     */
	private $method_separator;

	/**
	 * DefaultCallableStrategy constructor.
	 * @param string $namespace_separator
	 * @param string $method_separator
     */
	public function __construct($namespace_separator = null, $method_separator = null) {
		$this->namespace_separator = ($namespace_separator === null) ? self::DEFAULT_NAMESPACE_SEPARATOR : $namespace_separator;
		$this->method_separator = ($method_separator === null) ? self::DEFAULT_METHOD_SEPARATOR : $method_separator;
	}

	/**
	 * @param string $in
	 * @return array
	 * @throws \Exception
     */
	public function resolve($in)
	{
		$pos = strrpos($in, $this->method_separator);
		if ($pos === false) {
			throw new \Exception(sprintf('Method separator not found in %s', $in));
		}

		if ($pos === 0) {
			// Use backtrace to find the calling Class
			$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
			$class = $trace[2]['class'];
			$method = substr($in, $pos + strlen($this->method_separator));
		} else {
			$class = substr($in, 0, $pos);
			$method = substr($in, $pos + strlen($this->method_separator));
		}

		return [$class, $method];
	}
}