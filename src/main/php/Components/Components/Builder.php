<?php namespace Motorphp\SilexTools\Components\Components;

use Motorphp\SilexAnnotations\Common;
use Motorphp\SilexAnnotations\Reader\ConstantsReader;

use Motorphp\SilexTools\Components\Components;
use Motorphp\SilexTools\Components\Converter;
use Motorphp\SilexTools\Components\Controller;
use Motorphp\SilexTools\Components\Factory;
use Motorphp\SilexTools\Components\Key;
use Motorphp\SilexTools\Components\KeyFactories;
use Motorphp\SilexTools\Components\Provider;
use Motorphp\SilexTools\Components\Parameter;
use Swagger\Annotations\Operation;
use Motorphp\SilexTools\Components\ServiceCallback;

class Builder
{
    /** @var array | Key[] */
    private $keys;

    /** @var array | ServiceCallback\Binding[] */
    private $factories = [];

    /** @var array | Converter\Binding[] */
    private $converters = [];

    /** @var array | Controller\Binding[] */
    private $controllers = [];

    /** @var array | Parameter\Binding[] */
    private $parameters = [];

    /** @var array | Provider[] */
    private $providers = [];

    /** @var KeyFactories */
    private $keyFactories;

    /** @var Bindings */
    private $bindings;

    public static function instance() : Builder
    {
        $keyFactories = new Key\Factories();
        $bindings = new Bindings();
        return new Builder($keyFactories, $bindings);
    }

    public function __construct(KeyFactories $keyFactories, Bindings $bindings)
    {
        $this->keyFactories = $keyFactories;
        $this->bindings = $bindings;
    }

    public function addParameter(\ReflectionMethod $reflector, ConstantsReader $reader) : Builder
    {
        /** @var Common\Parameter $annotation */
        $annotation = $reader->getMethodAnnotation($reflector, Common\Parameter::class);
        $binding = $this->bindings->parameter($annotation, $reflector);
        $this->parameters[] = $binding;

        return $this;
    }

    public function addKey(\ReflectionClassConstant $reflector, ConstantsReader $reader) : Builder
    {
        $serviceKey = null;

        /** @var Common\ContainerKey $annotation */
        $annotation = $reader->getConstantAnnotation($reflector, Common\ContainerKey::class);
        if (empty($annotation->service)) {
            $serviceKey = $reflector->getDeclaringClass()->getName();
        } else {
            $serviceKey = $annotation->service;
        }
        $component = new Key\ConstantKey($serviceKey, $reflector);

        $this->keys[] = $component;
        return $this;
    }

    public function addController(\ReflectionMethod $reflector, ConstantsReader $reader) : Builder
    {
        $annotations = $reader->getMethodAnnotations($reflector);
        /** @var Operation[] $operations */
        $operations = array_filter($annotations, function ($o) {
            return $o instanceof Operation;
        });
        if (count($operations) !== 1) {
            throw new \RuntimeException('two many operations');
        }
        $operation = array_pop($operations);
        $binding = $this->bindings->controllerBinding($operation, $reflector);
        $this->controllers[] = $binding;

        // add each parameter to a builder
        return $this;
    }

    public function addConverter(\ReflectionMethod $reflector, ConstantsReader $reader) : Builder
    {
        /** @var Common\ParamConverter $annotation */
        $annotation = $reader->getMethodAnnotation($reflector, Common\ParamConverter::class);
        $binding = $this->bindings->converter($annotation, $reflector);
        $this->converters[] = $binding;
        return $this;
    }

    public function addFactory(\ReflectionMethod $reflector, ConstantsReader $reader) : Builder
    {
        /** @var Common\ServiceFactory $annotation */
        $annotation = $reader->getMethodAnnotation($reflector, Common\ServiceFactory::class);
        $this->factories[] = $this->bindings->factory($annotation, $reflector);
        return $this;
    }

    public function addProvider(\ReflectionClass $reflector, ConstantsReader $reader) : Builder
    {
        $this->providers[] = new Provider($reflector);
        return $this;
    }

    /**
     * @return Components
     */
    public function build() : Components
    {
        $keysBuilder = new Key\LookupFactories($this->keyFactories, $this->keys);
        $components = array_merge([], $this->providers);

        foreach ($this->factories as $binding) {
            $builder = new Factory\Builder();
            $components[] = $builder->withCallback(
                $binding->resolveKey($keysBuilder),
                $binding->getMethod()
            )->build();
        }

        foreach ($this->parameters as $binding) {
            $components[] = $binding->configureBuilder(new Parameter\Builder())
                ->withCallback(
                    $binding->resolveKey($keysBuilder),
                    $binding->getMethod()
                )->build();
        }

        foreach ($this->controllers as $binding) {
            $components[] = $binding->configureBuilder(new Controller\Builder())
                ->withCallback(
                    $binding->resolveKey($keysBuilder),
                    $binding->getMethod()
                )->build();
        }

        foreach ($this->converters as $binding) {
            foreach ($this->controllers as $controllerBinding) {
                foreach ($controllerBinding->getParams() as $param) {
                    if ($binding->matches($param)) {
                        $builder = new Converter\Builder();
                        $components[] = $builder->setParam($param)->withCallback(
                            $binding->resolveKey($keysBuilder),
                            $binding->getMethod()
                        )->build();
                    }
                }
            }
        }

        return new Components($components);
    }
}