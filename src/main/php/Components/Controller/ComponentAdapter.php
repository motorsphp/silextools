<?php namespace Motorphp\SilexTools\Components\Controller;

use Motorphp\SilexTools\Components\ServiceCallback;
use Motorphp\SilexTools\Components\Component;
use Motorphp\SilexTools\Components\ComponentsVisitor;
use Motorphp\SilexTools\Components\Controller;
use Motorphp\SilexTools\Components\Key;
use Motorphp\SilexTools\Components\SourceCodeWriter;
use Motorphp\SilexTools\Components\Value;

class ComponentAdapter implements Component, ServiceCallback
{
    /** @var Controller */
    private $component;

    /** @var Key */
    private $key;

    /** @var \ReflectionMethod */
    private $method;

    public function __construct(Controller $component, Key $key, \ReflectionMethod $method)
    {
        $this->component = $component;
        $this->key = $key;
        $this->method = $method;
    }

    function writeKey(SourceCodeWriter $writer) : Value
    {
        return $this->key->write($writer);
    }

    function writeMethod(SourceCodeWriter $writer) : Value
    {
        return $writer->writeString($this->method->name);
    }

    function acceptVisit(ComponentsVisitor $from)
    {
        $from->visitController($this, $this->component);
    }
}