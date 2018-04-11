<?php namespace Resource\Providers;

use Pimple\ServiceProviderInterface;

class AnotherDummyProvider implements ServiceProviderInterface
{

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param \Pimple\Container $pimple A container instance
     */
    public function register(\Pimple\Container $pimple)
    {
        // TODO: Implement register() method.
    }
}