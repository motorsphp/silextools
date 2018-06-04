<?php namespace Motorphp\SilexTools\Components;

class Value
{
    private $value;

    /**
     * Value constructor.
     * @param $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function asLiteral() : string
    {
        return $this->value;
    }


}