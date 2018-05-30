<?php namespace Motorphp\SilexTools\Components\ServiceCallback;

use Motorphp\SilexTools\Components\Key;
use Motorphp\SilexTools\Components\KeyFactories;

class BindingClassnameKey implements Binding
{
    /** @var \ReflectionClass */
    private $key;

    /** @var \ReflectionMethod */
    private $method;

    /**
     * BindingClassnameKey constructor.
     * @param \ReflectionClass $key
     * @param \ReflectionMethod $method
     */
    public function __construct(\ReflectionClass $key, \ReflectionMethod $method)
    {
        $this->key = $key;
        $this->method = $method;
    }

    public function resolveKey(KeyFactories $keys): Key
    {
        return $keys->fromClassName($this->key);
    }

    public function getMethod(): \ReflectionMethod
    {
        return $this->method;
    }
}