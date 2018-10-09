<?php namespace Motorphp\SilexTools\Components;

class ComponentsVisitorGroup implements ComponentsVisitor
{
    /** @var array | ComponentsVisitor[] */
    private $visitors;

    /**
     * ComponentsVisitorGroup constructor.
     * @param array|ComponentsVisitor[] $visitors
     */
    public function __construct($visitors)
    {
        $this->visitors = $visitors;
    }

    function visitController(ServiceCallback $callback, Controller $service)
    {
        foreach ($this->visitors as $visitor) {
            $visitor->visitController($callback, $service);
        }
    }

    function visitParameter(ServiceCallback $callback, Parameter $service)
    {
        foreach ($this->visitors as $visitor) {
            $visitor->visitParameter($callback, $service);
        }
    }

    function visitConverter(ServiceCallback $callback, Converter $service)
    {
        foreach ($this->visitors as $visitor) {
            $visitor->visitConverter($callback, $service);
        }
    }

    function visitFactory(Factory $component)
    {
        foreach ($this->visitors as $visitor) {
            $visitor->visitFactory($component);
        }
    }

    function visitProvider(Provider $component)
    {
        foreach ($this->visitors as $visitor) {
            $visitor->visitProvider($component);
        }
    }
}