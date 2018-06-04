<?php namespace Motorphp\SilexTools\Components\Key;

use Motorphp\SilexTools\Components\Key;
use Motorphp\SilexTools\Components\SourceCodeWriter;
use Motorphp\SilexTools\Components\Value;

/**
 * A service key which is a defined as a class constant or a class name
 */
class ConstantKey implements Key
{
    /** @var string */
    private $id;

    /** @var \ReflectionClassConstant */
    private $reflector;

    public function  __construct(string $id, \ReflectionClassConstant $reflector)
    {
        $this->id = $id;
        $this->reflector = $reflector;
    }

    function getId() : string
    {
        return $this->id;
    }

    function write(SourceCodeWriter $writer) : Value
    {
        return $writer->writeConstant($this->reflector);
    }


}