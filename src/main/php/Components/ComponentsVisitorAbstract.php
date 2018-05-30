<?php namespace Motorphp\SilexTools\Components;

class ComponentsVisitorAbstract implements ComponentsVisitor
{
    function visitController(ServiceCallback $callback, Controller $service)
    {
        // TODO: Implement visitController() method.
    }

    function visitParameter(ServiceCallback $callback, Parameter $service)
    {
        // TODO: Implement visitParameter() method.
    }

    function visitConverter(ServiceCallback $callback, Converter $service)
    {
        // TODO: Implement visitConverter() method.
    }

    function visitFactory(Factory $component)
    {
        // TODO: Implement visitFactory() method.
    }

    function visitProvider(Provider $component)
    {
        // TODO: Implement visitProvider() method.
    }
}