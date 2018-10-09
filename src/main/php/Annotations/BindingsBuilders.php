<?php namespace Motorphp\SilexTools\Annotations;

use Motorphp\SilexAnnotations\Common;
use Motorphp\SilexAnnotations\Common\Service;
use Motorphp\SilexAnnotations\Reader\ConstantsReader;
use Motorphp\SilexTools\Components;

use Swagger\Annotations\Operation;


class BindingsBuilders
{
    /** @var array | Components\Key[] */
    private $keys;

    /** @var array | Components\ServiceCallback\Binding[] */
    private $factories = [];

    /** @var array | Components\Converter\Binding[] */
    private $converters = [];

    /** @var array | Components\Controller\Binding[] */
    private $controllers = [];

    /** @var array | Components\Parameter\Binding[] */
    private $parameters = [];

    /** @var array | Components\Provider[] */
    private $providers = [];

    /** @var Components\KeyFactories */
    private $keyFactories;

    public static function instance() : BindingsBuilders
    {
        $keyFactories = new Components\Key\Factories();
        return new BindingsBuilders($keyFactories);
    }

    public function __construct(Components\KeyFactories $keyFactories)
    {
        $this->keyFactories = $keyFactories;
    }

    public function addParameter(\ReflectionMethod $reflector, ConstantsReader $reader) : BindingsBuilders
    {
        /** @var Common\Parameter $annotation */
        $annotation = $reader->getMethodAnnotation($reflector, Common\Parameter::class);
        $processor = new ParameterProcessor();
        $this->parameters[] = $processor->binding($annotation, $reflector);

        return $this;
    }


    public function addKey(\ReflectionClassConstant $reflector, ConstantsReader $reader) : BindingsBuilders
    {
        $serviceKey = null;

        /** @var Common\ContainerKey $annotation */
        $annotation = $reader->getConstantAnnotation($reflector, Common\ContainerKey::class);
        if (empty($annotation->service)) {
            $serviceKey = $reflector->getDeclaringClass()->getName();
        } else {
            $serviceKey = $annotation->service;
        }
        $component = new Components\Key\ConstantKey($serviceKey, $reflector);

        $this->keys[] = $component;
        return $this;
    }

    public function addController(\ReflectionMethod $reflector, ConstantsReader $reader) : BindingsBuilders
    {
        $annotations = $reader->getMethodAnnotations($reflector);
        /** @var Operation[] $operations */
        $operations = array_filter($annotations, function ($o) {
            return $o instanceof Operation;
        });
        if (count($operations) !== 1) {
            throw new \RuntimeException('two many operations');
        }
        $annotation = array_pop($operations);
        $processor = new ControllerProcessor();
        $this->controllers[] = $processor->binding($annotation, $reflector);

        // add each parameter to a builder
        return $this;
    }

    public function addConverter(\ReflectionMethod $reflector, ConstantsReader $reader) : BindingsBuilders
    {
        /** @var Common\ParamConverter $annotation */
        $annotation = $reader->getMethodAnnotation($reflector, Common\ParamConverter::class);
        $processor = new ConverterProcessor();
        $this->converters[] = $processor->binding($annotation, $reflector);
        return $this;
    }

    public function addFactory(\ReflectionMethod $reflector, ConstantsReader $reader) : BindingsBuilders
    {
        /** @var Common\ServiceFactory $annotation */
        $annotation = $reader->getMethodAnnotation($reflector, Common\ServiceFactory::class);
        /** @var Common\FactoryCapabilities $capabilities */
        $capabilities = $reader->getMethodAnnotation($reflector, Common\FactoryCapabilities::class);

        $processor = new FactoryProcessor();
        $this->factories[] = $processor->binding($annotation, $capabilities, $reflector);
        return $this;
    }

    public function addProvider(\ReflectionClass $reflector, ConstantsReader $reader) : BindingsBuilders
    {
        /** @var Components\Key $key */
        $key = null;

        /** @var Service $service */
        $service = $reader->getClassAnnotation($reflector, Service::class);
        if ($service && $service->name) {
            $key = new Components\Key\ScalarKey($service->name);
        }
        if (empty($key)) {
            $key = new Components\Key\ClassnameKey($reflector->getName(), $reflector);
        }

        $provider = new Components\Provider($key, $reflector);
        $this->providers[] = new Components\Provider\ComponentAdapter($provider);
        return $this;
    }

    /**
     * @return Components\Components
     */
    public function build() : Components\Components
    {
        $components = array_reduce($this->buildAll(), function (array $head, array $tail) {
            return array_merge($head, $tail);
        }, []);

        return new Components\Components($components);
    }

    private function buildAll() : array
    {
        $keysBuilder = new Components\Key\LookupFactories($this->keyFactories, $this->keys);
        return [
            $this->providers,
            $this->buildFactories($keysBuilder),
            $this->buildControllers($keysBuilder),
            $this->buildConverters($keysBuilder),
            $this->buildParameters($keysBuilder)
        ];
    }

    private function buildFactories(Components\Key\LookupFactories $keys) : array
    {
        $processor = new FactoryProcessor();
        return $processor->components($this->factories, $keys);
    }

    private function buildParameters(Components\Key\LookupFactories $keys) : array
    {
        $processor = new ParameterProcessor();
        return $processor->components($this->parameters, $keys);
    }

    private function buildControllers(Components\Key\LookupFactories $keys) : array
    {
        $processor = new ControllerProcessor();
        return $processor->components($this->controllers, $keys);
    }

    private function buildConverters(Components\Key\LookupFactories $keys) : array
    {
        $processor = new ConverterProcessor();
        return $processor->components($this->converters, $this->controllers, $keys);
    }


}