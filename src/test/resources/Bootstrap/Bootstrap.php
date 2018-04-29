<?php namespace Resource\Bootstrap;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Resource\Http\HealthCheckFactories;

class Bootstrap
{
    /**
     * @var ServiceProviderInterface[]
     */
    private $providers;

    public function configureProviders(Container $container, array $env, \DateTime $now)
    {
        /**
            $provider = new ?();
            $provider->register($container);
         */

        foreach ($this->providers as $provider) {
            $provider->register($container);
        }
    }

    public function configureFactories(Container $container, array $env, \DateTime $now)
    {
        $container->offsetSet(HealthCheckFactories::CONTAINER_KEY, function (Container $container) {
            return HealthCheckFactories::factory($container);
        });
        $container->offsetSet(HealthCheckFactories::CONTAINER_KEY, 'HealthCheckFactories::factory');

        /**
            $container->offsetSet(?, function (Container $container) {
                return ?($container);
            });
         */


    }
}
