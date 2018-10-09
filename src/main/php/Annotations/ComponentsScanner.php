<?php namespace Motorphp\SilexTools\Annotations;

use Motorphp\PhpScan\Criteria\Criteria;
use Motorphp\PhpScan\Criteria\Query;
use Motorphp\PhpScan\ObjectQueryStream\ConsumerBuilder;
use Motorphp\PhpScan\ObjectQueryStream\Consumers;
use Motorphp\PhpScan\Stream\ClassFileConsumer;
use Motorphp\PhpScan\Stream\Consumer;
use Motorphp\PhpScan\Stream\Streams;
use Motorphp\SilexAnnotations\Common\ContainerKey;
use Motorphp\SilexAnnotations\Common\ParamConverter;
use Motorphp\SilexAnnotations\Common\Parameter;
use Motorphp\SilexAnnotations\Common\ServiceFactory;
use Motorphp\SilexAnnotations\Reader\ConstantsReader;
use Motorphp\SilexTools\Annotations;
use Motorphp\SilexTools\Components;

use Pimple\ServiceProviderInterface;
use Swagger\Annotations\Delete;
use Swagger\Annotations\Get;
use Swagger\Annotations\Post;
use Swagger\Annotations\Put;

class ComponentsScanner
{
    /** @var ConstantsReader */
    private $reader;

    /** @var array|\Closure[] */
    private $consumers = [];

    /** @var BindingsBuilders */
    private $components;

    static public function createDefault(ConstantsReader $reader) : ComponentsScanner
    {
        $reader = new ComponentsScanner($reader);
        $reader
            ->factoriesDefault()
            ->providersDefault(ServiceProviderInterface::class)
            ->controllersDefault([Get::class, Post::class, Put::class, Delete::class])
            ->convertersDefault()
        ;
        return $reader;
    }

    public function __construct(ConstantsReader $reader)
    {
        $this->reader = $reader;
    }

    private function collectProviders()
    {
        return function (array $reflectors) {
            foreach ($reflectors as $reflector) {
                $this->components->addProvider($reflector, $this->reader);
            }
        };
    }

    public function providers(Query $query) : ComponentsScanner
    {
        $this->consumers[__FUNCTION__] = Consumers::buildWithDefaultDataModel()
            ->setQuery($query)
            ->setOutputNotEmpty($this->collectProviders())
            ->build()
        ;

        return $this;
    }

    public function providersDefault($interface) : ComponentsScanner
    {
        return $this->providers(
                Criteria::factory(\ReflectionClass::class, 'c')
                    ->where(
             //           Consumers::expr()->implements('c', ServiceProviderInterface::class)
                            Consumers::expr()->implements('c', $interface)
                    )
                    ->build()
            )
        ;
    }

    private function collectControllers() : \Closure
    {
        return function (array $reflectors) {
            foreach ($reflectors as $reflector) {
                $this->components->addController($reflector, $this->reader);
            }
        };
    }

    public function controllers(Query $query) : ComponentsScanner
    {
        $this->consumers[__FUNCTION__] = Consumers::buildWithDefaultDataModel()
            ->setQuery($query)
            ->setOutputNotEmpty($this->collectControllers())
            ->build()
        ;

        return $this;
    }

    public function controllersDefault($annotations): ComponentsScanner
    {
        return $this->controllers(
            Criteria::factory(\ReflectionMethod::class, 'c')
                ->andWhere(
                //Consumers::expr()->hasAnyAnnotations('c', [Get::class, Post::class, Put::class, Delete::class])
                    Consumers::expr()->hasAnyAnnotations('c', $annotations)
                )
                ->build()
        );
    }

    private function collectFactories() : \Closure
    {
        return function (array $reflectors) {
            foreach ($reflectors as $reflector) {
                $this->components->addFactory($reflector, $this->reader);
            }
        };
    }

    public function factories(Query $query) : ComponentsScanner
    {
        $this->consumers[__FUNCTION__] = Consumers::buildWithDefaultDataModel()
            ->setQuery($query)
            ->setOutputNotEmpty($this->collectFactories())
            ->build()
        ;

        return $this;
    }

    public function factoriesDefault(): ComponentsScanner
    {
        return $this->factories(
            Criteria::factory(\ReflectionMethod::class, 'c')
                ->andWhere(
                    Consumers::expr()->hasAnnotations('c', ServiceFactory::class),
                    Consumers::expr()->hasModifiers('c', \ReflectionMethod::IS_STATIC )
                )
                ->build()
        );
    }

    private function collectConverters() : \Closure
    {
        return function (array $reflectors) {
            foreach ($reflectors as $reflector) {
                $this->components->addConverter($reflector, $this->reader);
            }
        };
    }

    public function converters(Query $query) : ComponentsScanner
    {
        $this->consumers[__FUNCTION__] = Consumers::buildWithDefaultDataModel()
            ->setQuery($query)
            ->setOutputNotEmpty($this->collectConverters())
            ->build()
        ;

        return $this;
    }

    public function convertersDefault(): ComponentsScanner
    {
        return $this->converters(
            Criteria::factory(\ReflectionMethod::class, 'c')
                ->andWhere(
                    Consumers::expr()->hasAnnotations('c', ParamConverter::class)
                )
                ->build()
        );
    }

    private function collectParameters() : \Closure
    {
        return function (array $reflectors) {
            foreach ($reflectors as $reflector) {
                $this->components->addParameter($reflector, $this->reader);
            }
        };
    }

    public function parameters(Query $query) : ComponentsScanner
    {
        $this->consumers[__FUNCTION__] = Consumers::buildWithDefaultDataModel()
            ->setQuery($query)
            ->setOutputNotEmpty($this->collectParameters())
            ->build()
        ;

        return $this;
    }

    public function parametersDefault(): ComponentsScanner
    {
        return $this->parameters(
                Criteria::factory(\ReflectionMethod::class, 'c')
                    ->andWhere(
                        Consumers::expr()->hasAnnotations('c', Parameter::class)
                    )
                    ->build()
            )
        ;
    }

    private function collectKeys() : \Closure
    {
        return function (array $reflectors) {
            foreach ($reflectors as $reflector) {
                $this->components->addKey($reflector, $this->reader);
            }
        };
    }

    private function keys() : array
    {
        $consumers = [];
        $consumers[__FUNCTION__] = Consumers::buildWithDefaultDataModel()
            ->setQuery(
                Criteria::factory(\ReflectionClassConstant::class, 'c')
                    ->where(
                        Consumers::expr()->hasAnnotations('c', ContainerKey::class)
                    )
                    ->build()
            )
            ->setOutputNotEmpty($this->collectKeys())
            ->build()
        ;

        return $consumers;
    }

    public function scanWithConfig(ScanConfig $config)
    {
        $components = Annotations\BindingsBuilders::instance();
        $this->components = $components;

        $consumers = [];
        $consumer = $config->providersConsumer(Consumers::buildWithDefaultDataModel());
        if ($consumer) {
            $consumers[] = $consumer->setOutput($this->collectProviders());
        }
    }

    /**
     * @param array | string[] $sources
     * @return Components\Components
     */
    public function scan(array $sources) : Components\Components
    {
        $components = Annotations\BindingsBuilders::instance();
        $this->components = $components;

        $consumers = array_merge($this->consumers, $this->keys());
        $consumer = new ClassFileConsumer($consumers);
        Streams::classFiles($sources)->consume($consumer);

        $result = $components->build();
        $this->components = null;

        return $result;
    }
}