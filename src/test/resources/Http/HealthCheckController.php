<?php namespace Resource\Http;

use Silex;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as SWG;

class HealthCheckController
{
    /**
     * @SWG\Get(
     *     path="/monitoring/healthcheck",
     *     operationId="monitoring/healthcheck",
     *     description="Returns information about the health of the api",
     *     @SWG\Response(
     *         response=200,
     *         description="greeting response",
     *         @SWG\Schema(ref="#/definitions/HealthCheck")
     *     )
     * )
     */
    public function get(Request $request, Silex\Application $app) : HealthCheck
    {
        return new HealthCheck('healthy');
    }
}
