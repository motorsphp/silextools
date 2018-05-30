<?php namespace Motorphp\SilexTools\Components;

interface KeyFactories
{
    function fromString(string $source) : Key;

    function fromClassName(\ReflectionClass $source) : Key;

    function fromConstant(\ReflectionClassConstant $source) : Key;
}