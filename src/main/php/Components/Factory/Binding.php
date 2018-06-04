<?php namespace Motorphp\SilexTools\Components\Factory;

use Motorphp\SilexTools\Components\Key;
use Motorphp\SilexTools\Components\KeyFactories;
use Motorphp\SilexTools\Components\ServiceCallback;

class Binding implements ServiceCallback\Binding
{
    private $provider = '';

    private $capabilities = [];

    /** @var ServiceCallback\Binding */
    private $callbackBinding;

    public function __construct(ServiceCallback\Binding $callbackBinding, string $placement = '', array $capabilities = [])
    {
        $this->provider = $placement;
        $this->capabilities = $capabilities;
        $this->callbackBinding = $callbackBinding;
    }

    public function configureBuilder(Builder $builder) : Builder
    {
        if (!empty($this->provider)) {
            $builder->withProviderPlacement($this->provider);
        }

        if (! empty($this->capabilities)) {
            $builder->withCapabilities($this->capabilities);
        }

        return $builder;
    }

    public function resolveKey(KeyFactories $keys): Key
    {
        return $this->callbackBinding->resolveKey($keys);
    }

    public function getMethod(): \ReflectionMethod
    {
        return $this->callbackBinding->getMethod();
    }
}