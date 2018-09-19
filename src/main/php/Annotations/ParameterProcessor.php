<?php namespace Motorphp\SilexTools\Annotations;

use Motorphp\SilexAnnotations\Common;
use Motorphp\SilexTools\Components\Component;
use Motorphp\SilexTools\Components\KeyFactories;
use Motorphp\SilexTools\Components\Parameter;

class ParameterProcessor
{
    public function binding(Common\Parameter $annotation, \ReflectionMethod $reflector): Parameter\Binding
    {
        $default = empty($annotation->default) ? "" : $annotation->default;
        $name = empty($annotation->name) ? $reflector->getName() : $annotation->name;

        return new Parameter\Binding($name, $default, $reflector);
    }

    /**
     * @param array | Parameter\Binding[] $bindings
     * @param KeyFactories $keysBuilder
     * @return array|Component[]
     */
    public function components(array $bindings, KeyFactories $keysBuilder) : array
    {
        $components = [];
        foreach ($bindings as $binding) {
            $components[] = $binding->configureBuilder(new Parameter\Builder())
                ->withCallback(
                    $binding->resolveKey($keysBuilder),
                    $binding->getMethod()
                )->build();
        }

        return $components;
    }
}