<?php namespace Resource\Http;

use Pimple\Container;
use Symfony\Component\HttpFoundation\Request;

use Motorphp\SilexAnnotations\Common\ContainerKey;
use Motorphp\SilexAnnotations\Common\ServiceFactory;
use Motorphp\SilexAnnotations\Common\ControllerFactory;
use Motorphp\SilexAnnotations\Common\ParamConverter;

class HealthCheckFactories
{
    /**
     * @ContainerKey
     */
    const CONTAINER_KEY = HealthCheckFactories::class;

    /**
     * @ServiceFactory
     *
     * @param Container $app
     * @return HealthCheckFactories
     */
    public static function factory(Container $app) : HealthCheckFactories
    {
        return new HealthCheckFactories();
    }

    /**
     * @ControllerFactory
     *
     * @param Container $app
     * @return HealthCheckController
     */
    public function controller(Container $app) : HealthCheckController
    {
        return new HealthCheckController();
    }

    public function view(HealthCheck $healthCheck, Request $request): string
    {
        return "";
    }
}