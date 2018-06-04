<?php namespace Motorphp\SilexTools\Components;

use Motorphp\SilexTools\Components\Component;
use Motorphp\SilexTools\Components\ComponentsVisitor;
use Motorphp\SilexTools\Components\SourceCodeWriter;

class Provider
{
    /** @var Key */
    private $key;

    /** @var \ReflectionClass */
    private $reflector;

    /**
     * Provider constructor.
     * @param Key $key
     * @param \ReflectionClass $reflector
     */
    public function __construct(Key $key, \ReflectionClass $reflector)
    {
        $this->key = $key;
        $this->reflector = $reflector;
    }

    /**
     * @return Key
     */
    public function getId(): Key
    {
        return $this->key;
    }

    public function writeClass(SourceCodeWriter $writer)
    {
        $writer->writeClassType($this->reflector);
    }
}