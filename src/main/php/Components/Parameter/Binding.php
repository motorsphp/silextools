<?php namespace Motorphp\SilexTools\Components\Parameter;

use Motorphp\SilexTools\Components\ServiceCallback;
use Motorphp\SilexTools\Components\Key;
use Motorphp\SilexTools\Components\KeyFactories;

class Binding implements ServiceCallback\Binding
{
    /** @var string */
    private $name;

    /** @var string */
    private $default;

    /** @var \ReflectionMethod */
    private $method;

    /**
     * Binding constructor.
     * @param string $name
     * @param string $default
     * @param \ReflectionMethod $method
     */
    public function __construct(string $name, string $default, \ReflectionMethod $method)
    {
        $this->name = $name;
        $this->default = $default;
        $this->method = $method;
    }

    public function configureBuilder(Builder $builder) : Builder
    {
        $builder->setName($this->name)->setDefault($this->default);
        return $builder;
    }

    public function resolveKey(KeyFactories $keys): Key
    {
        $clazz = $this->method->getDeclaringClass();
        return $keys->fromClassName($clazz);
    }

    public function getMethod(): \ReflectionMethod
    {
        return $this->method;
    }
}