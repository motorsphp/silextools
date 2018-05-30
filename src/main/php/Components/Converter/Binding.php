<?php namespace Motorphp\SilexTools\Components\Converter;

use Motorphp\SilexTools\Components\ServiceCallback;
use Motorphp\SilexTools\Components\Controller;
use Motorphp\SilexTools\Components\Key;
use Motorphp\SilexTools\Components\KeyFactories;

class Binding implements ServiceCallback\Binding
{
    /** @var ServiceCallback\Binding */
    private $callbackBinding;

    /** @var Controller\ParamPattern */
    private $paramPattern;

    /**
     * Binding constructor.
     * @param Controller\ParamPattern $paramPattern
     * @param ServiceCallback\Binding $callbackBinding
     */
    public function __construct(Controller\ParamPattern $paramPattern, ServiceCallback\Binding $callbackBinding)
    {
        $this->paramPattern = $paramPattern;
        $this->callbackBinding = $callbackBinding;
    }

    /**
     * @param Controller\Param $param
     * @return bool
     */
    public function matches(Controller\Param $param) : bool
    {
        return $this->paramPattern->matches($param);
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
