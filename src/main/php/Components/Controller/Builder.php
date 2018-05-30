<?php namespace Motorphp\SilexTools\Components\Controller;

use Motorphp\SilexTools\Components\Controller;
use Motorphp\SilexTools\Components\Key;

class Builder
{
    /** @var \ReflectionMethod */
    private $callback;

    /** @var Key */
    private $key;

    /** @var string */
    private $operationId;

    /** @var string */
    private $httpMethod;

    /** @var string */
    private $endpoint;

    /** @var array|string[]  */
    private $params = [];

    public function setParams(array $params): Builder
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @param string $operationId
     * @return Builder
     */
    public function setOperationId(string $operationId): Builder
    {
        $this->operationId = $operationId;
        return $this;
    }

    /**
     * @param string $httpMethod
     * @return Builder
     */
    public function setHttpMethod(string $httpMethod): Builder
    {
        $this->httpMethod = $httpMethod;
        return $this;
    }

    /**
     * @param string $endpoint
     * @return Builder
     */
    public function setEndpoint(string $endpoint): Builder
    {
        $this->endpoint = $endpoint;
        return $this;
    }

    public function withCallback(Key $key, \ReflectionMethod $method) : Builder
    {
        $this->key = $key;
        $this->callback = $method;

        return $this;
    }

    public function build() : CallbackComponent
    {
        $controller = new Controller(
            $this->operationId,
            $this->httpMethod,
            $this->endpoint,
            $this->params
        );

        return new CallbackComponent($controller, $this->key, $this->callback);
    }

}