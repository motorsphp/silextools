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

    public function writeName(SourceCodeWriter $writer)
    {
        $writer->writeString($this->name);
    }

    public function writeValue(SourceCodeWriter $writer)
    {
        $writer->writeString($this->default);
    }
}