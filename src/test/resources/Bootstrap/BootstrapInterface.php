<?php namespace Resource\Bootstrap;

use Pimple\Container;
use Silex\ControllerCollection;

interface BootstrapInterface
{
    function configureProviders(Container $container);

    function configureFactories(Container $container);

    function configureHttp(Container $container, ControllerCollection $controllers);
}