<?php namespace Motorphp\SilexTools\Components\Factory;

use Motorphp\SilexTools\Components\Component;
use Motorphp\SilexTools\Components\Factory;
use Motorphp\SilexTools\Components\Key;

class Builder
{
    /** @var Key */
    private $key;

    /** @var \ReflectionMethod */
    private $callback;

    /** @var Placement */
    private $placement;

    private $capabilities = [];

    public function withCallback(Key $key, \ReflectionMethod $callback) : Builder
    {
        $this->key = $key;
        $this->callback = $callback;
        return $this;
    }

    public function withCapabilities(array $capabilities) : Builder
    {
        $this->capabilities = $capabilities;
        return $this;
    }

    public function withProviderPlacement(string $provider) : Builder
    {
        $this->placement = new Placement($provider);
        return $this;
    }

    public function build() : Component
    {
        $placement = empty($this->placement) ? new Placement() : $this->placement;
        $capabilities = new Capabilities($this->capabilities);

        $factory = new Factory\DefaultFactory(
            $this->key,
            $this->callback,
            $placement,
            $capabilities
        );
        return new ComponentAdapter($factory);
    }
}