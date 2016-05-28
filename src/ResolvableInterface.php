<?php

namespace Softius\ResourcesResolver;

/**
 * Interface ResolvableInterface
 * @package Softius\ResourcesResolver
 */
interface ResolvableInterface {
    /**
     * @param string $in
     * @return mixed
     */
    public function resolve($in);
}