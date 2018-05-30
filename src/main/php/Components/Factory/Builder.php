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

    public function withCallback(Key $key, \ReflectionMethod $callback) : Builder
    {
        $this->key = $key;
        $this->callback = $callback;
        return $this;
    }

    public function build() : Component
    {
        $factory = new Factory($this->key, $this->callback);
        return new ComponentAdapter($factory);
    }
}