<?php namespace Motorphp\SilexTools\Components;

class Converter
{
    /** @var string */
    private $name;

    /** @var string */
    private $operation;

    /**
     * Converter constructor.
     * @param string $name
     * @param string $operation
     */
    public function __construct(string $name, string $operation)
    {
        $this->name = $name;
        $this->operation = $operation;
    }

    function writeName(SourceCodeWriter $writer) : Value
    {
        return $writer->writeString($this->name);
    }

    function getOperationId() : string
    {
        return $this->operation;
    }

}
