<?php

namespace Softius\ResourcesResolver;

/**
 * Interface StrategyAwareInterface
 * @package Softius\ResourcesResolver
 */
interface StrategyAwareInterface {
    /**
     * @param ResolvableInterface $strategy
     */
    public function setStrategy(ResolvableInterface $strategy);

    /**
     * @return ResolvableInterface
     */
    public function getStrategy();
}