<?php namespace Motorphp\SilexTools\Components;

interface ComponentsVisitor
{
    function visitController(ServiceCallback $callback, Controller $service);

    function visitParameter(ServiceCallback $callback, Parameter $service);

    function visitConverter(ServiceCallback $callback, Converter $service);

    function visitFactory(Factory $component);

    function visitProvider(Provider $component);
}