<?php namespace Motorphp\SilexTools\Components;

class Controller
{
    /** @var string */
    private $operationId;

    /** @var string */
    private $httpMethod;

    /** @var string */
    private $endpoint;

    /** @var array|string[]  */
    private $params;

    /**
     * Controller constructor.
     * @param string $operationId
     * @param string $httpMethod
     * @param string $endpoint
     * @param array $params
     */
    public function __construct(string $operationId, string $httpMethod, string $endpoint, array $params)
    {
        $this->operationId = $operationId;
        $this->httpMethod = $httpMethod;
        $this->endpoint = $endpoint;
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getOperationId(): string
    {
        return $this->operationId;
    }

    /**
     * @return array|string[]
     */
    public function getParams() : array
    {
        return $this->params;
    }

    public function writeHttpMethod(SourceCodeWriter $writer) : Value
    {
        return $writer->writeString($this->httpMethod);
    }

    public function writeEndpoint(SourceCodeWriter $writer) : Value
    {
        return $writer->writeString($this->endpoint);
    }
}