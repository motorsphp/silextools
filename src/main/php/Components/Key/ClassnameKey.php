<?php namespace Motorphp\SilexTools\Components\Key;

use Motorphp\SilexTools\Components\ComponentsVisitor;
use Motorphp\SilexTools\Components\Key;
use Motorphp\SilexTools\Components\ReflectorVisitor;
use Motorphp\SilexTools\Components\SourceCodeWriter;

/**
 * A service key which is a defined as a class constant or a class name
 */
class ClassnameKey implements Key
{
    /** @var string */
    private $id;

    /** @var \ReflectionClass */
    private $reflector;

    public function  __construct(string $id, \ReflectionClass $reflector)
    {
        $this->id = $id;
        $this->reflector = $reflector;
    }

    function getId() : string
    {
        return $this->id;
    }

    function write(SourceCodeWriter $writer)
    {
        $writer->writeClassName($this->reflector);
    }

}