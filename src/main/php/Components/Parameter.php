<?php namespace Motorphp\SilexTools\Components;

class Parameter
{
    /** @var string */
    private $name;

    /** @var string */
    private $default;

    public function __construct(string $name, string $default = "")
    {
        $this->name = $name;
        $this->default = $default;
    }

    public function writeName(SourceCodeWriter $writer) : Value
    {
        return $writer->writeString($this->name);
    }

    public function writeValue(SourceCodeWriter $writer) : Value
    {
        return $writer->writeString($this->default);
    }
}