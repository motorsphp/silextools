<?php namespace Motorphp\SilexTools\Components;

use Motorphp\SilexTools\Components\Component;
use Motorphp\SilexTools\Components\ComponentsVisitor;
use Motorphp\SilexTools\Components\SourceCodeWriter;

class Provider
{
    /** @var \ReflectionClass */
    private $reflector;

    /**
     * Provider constructor.
     * @param \ReflectionClass $reflector
     */
    public function __construct(\ReflectionClass $reflector)
    {
        $this->reflector = $reflector;
    }

    public function writeClass(SourceCodeWriter $writer)
    {
        $writer->writeClassType($this->reflector);
    }
}