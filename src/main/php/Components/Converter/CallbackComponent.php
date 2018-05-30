<?php namespace Motorphp\SilexTools\Components\Converter;

use Motorphp\SilexTools\Components\ServiceCallback;
use Motorphp\SilexTools\Components\Component;
use Motorphp\SilexTools\Components\ComponentsVisitor;
use Motorphp\SilexTools\Components\Converter;
use Motorphp\SilexTools\Components\Key;
use Motorphp\SilexTools\Components\SourceCodeWriter;

class CallbackComponent implements Component, ServiceCallback
{
    /** @var Converter */
    private $component;

    /** @var Key */
    private $key;

    /** @var \ReflectionMethod */
    private $method;

    public function __construct(Converter $component, Key $key, \ReflectionMethod $method)
    {
        $this->component = $component;
        $this->key = $key;
        $this->method = $method;
    }

    function writeKey(SourceCodeWriter $writer)
    {
        $this->key->write($writer);
    }

    function writeMethod(SourceCodeWriter $writer)
    {
        $writer->writeString($this->method->name);
    }

    function acceptVisit(ComponentsVisitor $from)
    {
        $from->visitConverter($this, $this->component);
    }
}