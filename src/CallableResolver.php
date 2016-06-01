<?php

namespace Softius\ResourcesResolver;

/**
 * Class CallableResolver.
 */
class CallableResolver implements StrategyAwareInterface
{
    use StrategyAwareTrait;

    /**
     * CallableResolver constructor.
     */
    public function __construct()
    {
        $this->setStrategy(new DefaultCallableStrategy());
    }

    /**
     * @param string $in
     *
     * @return mixed
     */
    public function resolve($in)
    {
        return $this->getStrategy()->resolve($in);
    }

    /**
     * @param string $in
     *
     * @return mixed
     */
    public function resolveSafe($in)
    {
        return $this->getStrategy()->resolveSafe($in);
    }
}
