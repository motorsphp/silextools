<?php namespace Motorphp\SilexTools\Components\ServiceCallback;

use Motorphp\SilexTools\Components\Key;
use Motorphp\SilexTools\Components\KeyFactories;

class BindingScalarKey implements Binding
{
    /** @var string */
    private $key;

    /** @var \ReflectionMethod */
    private $method;

    /**
     * BindingScalarKey constructor.
     * @param string $key
     * @param \ReflectionMethod $method
     */
    public function __construct(string $key, \ReflectionMethod $method)
    {
        $this->key = $key;
        $this->method = $method;
    }

    public function resolveKey(KeyFactories $keys): Key
    {
        return $keys->fromString($this->key);
    }

    public function getMethod(): \ReflectionMethod
    {
        return $this->method;
    }
}