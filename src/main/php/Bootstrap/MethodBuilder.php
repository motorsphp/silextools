<?php namespace Motorphp\SilexTools\Bootstrap;

use Motorphp\SilexTools\Components\Components;

interface MethodBuilder
{
    function withComponents(Components $components) : MethodBuilder;

    function done() : BootstrapBuilder;
}