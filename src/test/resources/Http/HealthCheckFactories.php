<?php namespace Resource\Http;

use Helstern\SMSkeleton\HttpApi\Serializer;
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
        /** @var Serializer $serializer */
        $serializer = $app[Serializer::class];
        return new HealthCheckFactories($serializer);
    }

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
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
        return $this->serializer->serialize($healthCheck, 'json');
    }
}