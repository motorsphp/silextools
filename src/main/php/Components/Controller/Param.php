<?php namespace Motorphp\SilexTools\Components\Controller;

class Param
{
    /** @var string */
    private $operationId;

    /** @var string */
    private $name;

    /** @var string */
    private $type;

    /**
     * Param constructor.
     * @param string $operationId
     * @param string $name
     * @param string $type
     */
    public function __construct(string $operationId, string $name, string $type)
    {
        $this->operationId = $operationId;
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getOperationId(): string
    {
        return $this->operationId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}