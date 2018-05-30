<?php namespace Motorphp\SilexTools\Components\ServiceCallback;

use Motorphp\SilexTools\Components\Key;
use Motorphp\SilexTools\Components\KeyFactories;

interface Binding
{
    public function resolveKey(KeyFactories $keys): Key;

    public function getMethod() : \ReflectionMethod;
}