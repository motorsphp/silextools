<?php namespace Motorphp\SilexTools\Components\Key;

use Motorphp\SilexTools\Components\Key;
use Motorphp\SilexTools\Components\KeyFactories;

class Factories implements KeyFactories
{
    function fromString(string $source) : Key
    {
        return new ScalarKey($source);
    }

    function fromClassName(\ReflectionClass $source) : Key
    {
        return new ClassnameKey($source->getNamespaceName(), $source);
    }

    function fromConstant(\ReflectionClassConstant $source) : Key
    {
        $id = $source->getDeclaringClass()->getName();
        return  new Key\ConstantKey($id, $source);
    }
}