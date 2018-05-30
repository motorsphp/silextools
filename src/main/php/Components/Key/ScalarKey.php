<?php namespace Motorphp\SilexTools\Components\Key;

use Motorphp\SilexTools\Components\ComponentsVisitor;
use Motorphp\SilexTools\Components\Key;
use Motorphp\SilexTools\Components\SourceCodeWriter;

class ScalarKey implements Key
{
    /** @var string */
    private $id;

    public function  __construct(string $id)
    {
        $this->id = $id;
    }

    function getId()
    {
        return $this->id;
    }

    function write(SourceCodeWriter $writer)
    {
        $writer->writeString($this->id);
    }

}