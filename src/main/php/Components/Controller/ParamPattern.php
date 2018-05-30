<?php namespace Motorphp\SilexTools\Components\Controller;

class ParamPattern
{
    /** @var string */
    private $operation;

    /** @var string */
    private $type;

    public function __construct(string $type, string $operation = null)
    {
        $this->operation = $operation;
        $this->type = $type;
    }

    function matches (Param $param)
    {
        $matchesOperation = empty($this->operationId) || $this->operationId === $param->getOperationId();
        $matchesType = $this->type === $param->getType();

        return $matchesOperation && $matchesType;
    }
}