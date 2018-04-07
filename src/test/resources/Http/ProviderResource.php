<?php namespace Resource\Http;

use Pimple\Container;
use Silex\Application as SilexApplication;
use Silex\ControllerCollection;

class ProviderResource
{
    public function register(Container $app)
    {
        $app[HealthCheckController::class] = function (Container $app) {
            return HealthCheckConverterFactory::createHealthCheckController($app);
        };

        $app[HealthCheckConverter::class] = function (Container $app) {
            return HealthCheckConverterFactory::createHealthCheckConverter($app);
        };
    }

    public function connectWithCollection(SilexApplication $app, ControllerCollection $controllers)
    {
        $healthcheckRenderer = implode(':', [HealthCheckConverter::class, 'convertToJson']);
        $app->view($healthcheckRenderer);

        $serviceMethod = implode(':', [HealthCheckController::class, 'get']);
        $controllers->get('/monitoring/healthcheck', $serviceMethod)->convert();
    }

    /**
     * Returns routes to connect to the given application.
     *
     * @param SilexApplication $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(SilexApplication $app)
    {
        /** @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory']; // creates a new controller based on the default route

        $this->connectWithCollection($app, $controllers);
        return $controllers;
    }
}
