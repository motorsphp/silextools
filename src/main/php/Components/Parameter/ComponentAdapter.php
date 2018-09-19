<?php namespace Motorphp\SilexTools\Components\Parameter;

use Motorphp\SilexTools\Components\ServiceCallback;
use Motorphp\SilexTools\Components\Component;
use Motorphp\SilexTools\Components\ComponentsVisitor;
use Motorphp\SilexTools\Components\Key;
use Motorphp\SilexTools\Components\Parameter;
use Motorphp\SilexTools\Components\SourceCodeWriter;
use Motorphp\SilexTools\Components\Value;

class ComponentAdapter implements Component, ServiceCallback
{
    /** @var Parameter */
    private $component;

    /** @var Key */
    private $key;

    /** @var \ReflectionMethod */
    private $method;

    /**
     * CallbackComponent constructor.
     * @param Parameter $component
     * @param Key $key
     * @param \ReflectionMethod $method
     */
    public function __construct(Parameter $component, Key $key, \ReflectionMethod $method)
    {
        $this->component = $component;
        $this->key = $key;
        $this->method = $method;
    }

    function acceptVisit(ComponentsVisitor $from)
    {
        $from->visitParameter($this, $this->component);
    }

    function writeKey(SourceCodeWriter $writer) : Value
    {
        return $this->key->write($writer);
    }

    function writeMethod(SourceCodeWriter $writer) : Value
    {
        return $writer->writeString($this->method->name);
    }
}