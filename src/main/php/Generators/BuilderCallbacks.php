<?php namespace Motorphp\SilexTools\Generators;

use Motorphp\SilexAnnotations\Reader\ConstantsReader;
use Motorphp\SilexTools\Components;

class BuilderCallbacks
{
    /** @var Components\AnnotationsBuilder */
    private $builder;

    /**
     * CallbackBuilder constructor.
     * @param Components\AnnotationsBuilder $builder
     */
    public function __construct(Components\AnnotationsBuilder $builder)
    {
        $this->builder = $builder;
    }

    public function addParameter(ConstantsReader $reader) : \Closure
    {
        return function (\ReflectionMethod $reflector) use($reader) {
            return $this->builder->addParameter($reflector, $reader);
        };
    }

    public function addKey(ConstantsReader $reader) : \Closure
    {
        return function (\ReflectionClassConstant $reflector) use($reader) {
            return $this->builder->addKey($reflector, $reader);
        };
    }

    public function addController(ConstantsReader $reader) : \Closure
    {
        return function (\ReflectionMethod $reflector) use($reader) {
            return $this->builder->addController($reflector, $reader);
        };
    }

    public function addConverter(ConstantsReader $reader) : \Closure
    {
        return function (\ReflectionMethod $reflector) use($reader) {
            return $this->builder->addConverter($reflector, $reader);
        };
    }

    public function addFactory(ConstantsReader $reader) : \Closure
    {
        return function (\ReflectionMethod $reflector) use($reader) {
            return $this->builder->addFactory($reflector, $reader);
        };
    }

    public function addProvider(ConstantsReader $reader) : \Closure
    {
        return function (\ReflectionClass $reflector) use($reader) {
            return $this->builder->addProvider($reflector, $reader);
        };
    }
}