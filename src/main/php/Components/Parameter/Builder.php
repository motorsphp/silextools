<?php namespace Motorphp\SilexTools\Components\Parameter;

use Motorphp\SilexTools\Components\Key;
use Motorphp\SilexTools\Components\Parameter;

class Builder
{
    /** @var string */
    private $name;

    /** @var string */
    private $default;

    /** @var Key */
    private $key;

    /** @var \ReflectionMethod */
    private $callback;

    /**
     * @param string $name
     * @return Builder
     */
    public function setName(string $name): Builder
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Builder
     */
    public function setReflectorName(\ReflectionMethod $name): Builder
    {
        $this->name = $name->getName();
        return $this;
    }

    /**
     * @param string $default
     * @return Builder
     */
    public function setDefault(string $default): Builder
    {
        $this->default = $default;
        return $this;
    }

    public function withCallback(Key $key, \ReflectionMethod $method) : Builder
    {
        $this->key = $key;
        $this->callback = $method;

        return $this;
    }

    public function build() : ComponentAdapter
    {
        $parameter = new Parameter($this->name, $this->default);
        return new ComponentAdapter($parameter, $this->key, $this->callback);
    }
}