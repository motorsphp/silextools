<?php namespace Motorphp\SilexTools\Components\Key;

use Motorphp\SilexTools\Components\Key;
use Motorphp\SilexTools\Components\SourceCodeWriter;
use Motorphp\SilexTools\Components\Value;

class ScalarKey implements Key
{
    /** @var string */
    private $id;

    public function  __construct(string $id)
    {
        $this->id = $id;
    }

    function getId() : string
    {
        return $this->id;
    }

    function write(SourceCodeWriter $writer) : Value
    {
        return $writer->writeString($this->id);
    }

}