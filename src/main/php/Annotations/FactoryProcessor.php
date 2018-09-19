<?php namespace Motorphp\SilexTools\Annotations;

use Motorphp\SilexAnnotations\Common;
use Motorphp\SilexTools\Components\Component;
use Motorphp\SilexTools\Components\Factory;
use Motorphp\SilexTools\Components\KeyFactories;
use Motorphp\SilexTools\Components\ServiceCallback;

class FactoryProcessor
{
    public function binding(
        Common\ServiceFactory $annotation,
        Common\FactoryCapabilities $capabilities = null,
        \ReflectionMethod $reflector
    ) : ServiceCallback\Binding
    {
        $callback = null;
        if ($annotation->service) {
            $callback = new ServiceCallback\BindingScalarKey($annotation->service, $reflector);
        } else {
            $callback = ServiceCallback\Bindings::returnType($reflector);
        }

        $capabilitiesMap = [] ;
        if (! empty($capabilities)) {
            $capabilitiesMap['firewall'] = $capabilities->firewall;
        }

        $provider = null;
        if (empty($annotation->provider)) {
            $provider = '';
        } else {
            $provider = $annotation->provider;
        }

        return new Factory\Binding($callback, $provider, $capabilitiesMap);
    }

    /**
     * @param array | Factory\Binding[] $bindings
     * @param KeyFactories $keysBuilder
     * @return array|Component[]
     */
    public function components(array $bindings, KeyFactories $keysBuilder) : array
    {
        $components = [];
        foreach ($bindings as $binding) {
            $builder = new Factory\Builder();
            $binding->configureBuilder($builder);

            $components[] = $builder
                ->withCallback(
                    $binding->resolveKey($keysBuilder),
                    $binding->getMethod()
                )->build();
        }

        return $components;
    }
}