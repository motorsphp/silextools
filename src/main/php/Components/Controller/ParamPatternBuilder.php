<?php namespace Motorphp\SilexTools\Components\Controller;

class ParamPatternBuilder
{
    /** @var string */
    private $operation;

    /** @var string */
    private $type;

    public function matchType(string $type) : ParamPatternBuilder
    {
        $this->type = $type;
        return $this;
    }

    public function matchAnyOperation() : ParamPatternBuilder
    {
        $this->operation = null;
        return $this;
    }

    public function matchOperation(string $operation) : ParamPatternBuilder
    {
        $this->operation = $operation;
        return $this;
    }

    public function build() : ParamPattern
    {
        return new ParamPattern($this->type, $this->operation);
    }
}