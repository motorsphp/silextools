<?php namespace Motorphp\SilexTools\Components\Converter;

use Motorphp\SilexTools\Components\Controller\Param;
use Motorphp\SilexTools\Components\Converter;
use Motorphp\SilexTools\Components\Key;

class Builder
{
    /** @var string */
    private $name;

    /** @var string */
    private $operation;

    /** @var Key */
    private $key;

    /** @var \ReflectionMethod */
    private $callback;

    public function setName(string $name) : Builder
    {
        $this->name = $name;
        return $this;
    }

    public function setOperation(string $name) : Builder
    {
        $this->operation = $name;
        return $this;
    }

    public function setParam(Param $param) : Builder
    {
        $this->setName($param->getName())->setOperation($param->getOperationId());
        return $this;
    }

    public function withCallback(Key $key, \ReflectionMethod $callback) : Builder
    {
        $this->key = $key;
        $this->callback = $callback;
        return $this;
    }

    public function build() : CallbackComponent
    {
        $component =  new Converter($this->name, $this->operation);
        return new CallbackComponent($component, $this->key, $this->callback);
    }

}
