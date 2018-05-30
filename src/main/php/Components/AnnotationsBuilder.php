<?php namespace Motorphp\SilexTools\Components;

use Motorphp\SilexAnnotations\Common;
use Motorphp\SilexAnnotations\Reader\ConstantsReader;
use Motorphp\SilexTools\Components\Annotations;

use Swagger\Annotations\Operation;

class AnnotationsBuilder
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

    public static function instance() : AnnotationsBuilder
    {
        $keyFactories = new Key\Factories();
        return new AnnotationsBuilder($keyFactories);
    }

    public function __construct(KeyFactories $keyFactories)
    {
        $this->keyFactories = $keyFactories;
    }

    public function addParameter(\ReflectionMethod $reflector, ConstantsReader $reader) : AnnotationsBuilder
    {
        /** @var Common\Parameter $annotation */
        $annotation = $reader->getMethodAnnotation($reflector, Common\Parameter::class);
        $processor = new Annotations\ParameterProcessor();
        $this->parameters[] = $processor->binding($annotation, $reflector);

        return $this;
    }

    public function addKey(\ReflectionClassConstant $reflector, ConstantsReader $reader) : AnnotationsBuilder
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

    public function addController(\ReflectionMethod $reflector, ConstantsReader $reader) : AnnotationsBuilder
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
        $processor = new Annotations\ControllerProcessor();
        $this->controllers[] = $processor->binding($annotation, $reflector);

        // add each parameter to a builder
        return $this;
    }

    public function addConverter(\ReflectionMethod $reflector, ConstantsReader $reader) : AnnotationsBuilder
    {
        /** @var Common\ParamConverter $annotation */
        $annotation = $reader->getMethodAnnotation($reflector, Common\ParamConverter::class);
        $processor = new Annotations\ConverterProcessor();
        $this->converters[] = $processor->binding($annotation, $reflector);
        return $this;
    }

    public function addFactory(\ReflectionMethod $reflector, ConstantsReader $reader) : AnnotationsBuilder
    {
        /** @var Common\ServiceFactory $annotation */
        $annotation = $reader->getMethodAnnotation($reflector, Common\ServiceFactory::class);
        $processor = new Annotations\FactoryProcessor();
        $this->factories[] = $processor->binding($annotation, $reflector);
        return $this;
    }

    public function addProvider(\ReflectionClass $reflector, ConstantsReader $reader) : AnnotationsBuilder
    {
        $this->providers[] = new Provider\ComponentAdapter(new Provider($reflector));
        return $this;
    }

    /**
     * @return Components
     */
    public function build() : Components
    {
        $components = array_reduce($this->buildAll(), function (array $head, array $tail) {
            return array_merge($head, $tail);
        }, []);

        return new Components($components);
    }

    private function buildAll() : array
    {
        $keysBuilder = new Key\LookupFactories($this->keyFactories, $this->keys);
        return [
            $this->providers,
            $this->buildFactories($keysBuilder),
            $this->buildControllers($keysBuilder),
            $this->buildConverters($keysBuilder),
            $this->buildParameters($keysBuilder)
        ];
    }

    private function buildFactories(Key\LookupFactories $keys) : array
    {
        $processor = new Annotations\FactoryProcessor();
        return $processor->components($this->factories, $keys);
    }

    private function buildParameters(Key\LookupFactories $keys) : array
    {
        $processor = new Annotations\ParameterProcessor();
        return $processor->components($this->parameters, $keys);
    }

    private function buildControllers(Key\LookupFactories $keys) : array
    {
        $processor = new Annotations\ControllerProcessor();
        return $processor->components($this->controllers, $keys);
    }

    private function buildConverters(Key\LookupFactories $keys) : array
    {
        $processor = new Annotations\ConverterProcessor();
        return $processor->components($this->converters, $this->controllers, $keys);
    }


}