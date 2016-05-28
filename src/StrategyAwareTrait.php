<?php

namespace Softius\ResourcesResolver;

/**
 * Class StrategyAwareTrait
 * @package Softius\ResourcesResolver
 */
trait StrategyAwareTrait {
    /**
     * @var ResolvableInterface
     */
    private $strategy;

    /**
     * @param ResolvableInterface $strategy
     */
    public function setStrategy(ResolvableInterface $strategy) {
        $this->strategy = $strategy;
    }

    /**
     * @return mixed
     */
    public function getStrategy() {
        return $this->strategy;
    }
}