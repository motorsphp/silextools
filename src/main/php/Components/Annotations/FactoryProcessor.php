<?php namespace Motorphp\SilexTools\Components\Annotations;

use Motorphp\SilexAnnotations\Common;
use Motorphp\SilexTools\Components\Component;
use Motorphp\SilexTools\Components\Factory;
use Motorphp\SilexTools\Components\KeyFactories;
use Motorphp\SilexTools\Components\ServiceCallback;

class FactoryProcessor
{
    public function binding(Common\ServiceFactory $annotation, \ReflectionMethod $reflector) : ServiceCallback\Binding
    {
        if ($annotation->service) {
            return new ServiceCallback\BindingScalarKey($annotation->service, $reflector);
        }

        return ServiceCallback\Bindings::returnType($reflector);
    }

    /**
     * @param array | ServiceCallback\Binding[] $bindings
     * @param KeyFactories $keysBuilder
     * @return array|Component[]
     */
    public function components(array $bindings, KeyFactories $keysBuilder) : array
    {
        $components = [];
        foreach ($bindings as $binding) {
            $builder = new Factory\Builder();
            $components[] = $builder->withCallback(
                $binding->resolveKey($keysBuilder),
                $binding->getMethod()
            )->build();
        }

        return $components;
    }
}