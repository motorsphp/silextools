<?php namespace Motorphp\SilexTools\Components\Controller;

use Motorphp\SilexTools\Components\ServiceCallback;
use Motorphp\SilexTools\Components\Key;
use Motorphp\SilexTools\Components\KeyFactories;

class Binding implements ServiceCallback\Binding
{
    /** @var string */
    private $operationId;

    /** @var string */
    private $httpMethod;

    /** @var string */
    private $endpoint;

    /** @var array|string[] */
    private $params;

    /** @var \ReflectionMethod */
    private $method;

    /**
     * Binding constructor.
     * @param string $operationId
     * @param string $httpMethod
     * @param string $endpoint
     * @param array|string[] $params
     * @param \ReflectionMethod $method
     */
    public function __construct(string $operationId, string $httpMethod, string $endpoint, $params, \ReflectionMethod $method)
    {
        $this->operationId = $operationId;
        $this->httpMethod = $httpMethod;
        $this->endpoint = $endpoint;
        $this->params = $params;
        $this->method = $method;
    }

    public function resolveKey(KeyFactories $keys): Key
    {
        $clazz = $this->method->getDeclaringClass();
        return $keys->fromClassName($clazz);
    }

    public function configureBuilder(Builder $builder) : Builder
    {
        $builder
            ->setOperationId($this->operationId)
            ->setEndpoint($this->endpoint)
            ->setHttpMethod($this->httpMethod)
            ->setParams($this->params)
        ;

        return $builder;
    }

    /**
     * @return array
     */
    public function getParams() : array
    {
        $params = [];
        foreach ($this->params as $name => $type) {
            $params[] = new Param($this->operationId, $name, $type);
        }

        return $params;
    }

    public function getMethod(): \ReflectionMethod
    {
        return $this->method;
    }
}