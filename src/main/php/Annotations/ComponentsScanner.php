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
        return $reader;
    }

    public function __construct(ConstantsReader $reader)
    {
        $this->reader = $reader;
    }

    private function configureProviders( ConsumerBuilder $builder) : ConsumerBuilder
    {
        return $builder->setOutput(
            function (array $reflectors) {
                foreach ($reflectors as $reflector) {
                    $this->components->addProvider($reflector, $this->reader);
                }
            }
        );
    }

    private function configureControllers( ConsumerBuilder $builder) : ConsumerBuilder
    {
        return $builder->setOutput(
            function (array $reflectors) {
                foreach ($reflectors as $reflector) {
                    $this->components->addController($reflector, $this->reader);
                }
            }
        );
    }

    private function configureFactories( ConsumerBuilder $builder) : ConsumerBuilder
    {
        return $builder->setOutput(
            function (array $reflectors) {
                foreach ($reflectors as $reflector) {
                    $this->components->addFactory($reflector, $this->reader);
                }
            }
        );
    }

    private function configureConverters( ConsumerBuilder $builder) : ConsumerBuilder
    {
        return $builder->setOutput(
            function (array $reflectors) {
                foreach ($reflectors as $reflector) {
                    $this->components->addConverter($reflector, $this->reader);
                }
            }
        );
    }

    private function configureParameters( ConsumerBuilder $builder) : ConsumerBuilder
    {
        return $builder->setOutput(
            function (array $reflectors) {
                foreach ($reflectors as $reflector) {
                    $this->components->addParameter($reflector, $this->reader);
                }
            }
        );
    }

    private function collectKeys() : \Closure
    {
        return function (array $reflectors) {
            foreach ($reflectors as $reflector) {
                $this->components->addKey($reflector, $this->reader);
            }
        };
    }

    /**
     * @return array
     * @throws \Exception
     */
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
        ;

        return $consumers;
    }

    /**
     * @param ScanConfig $config
     * @return Components\Components
     * @throws \Exception
     */
    public function scanWithConfig( ScanConfig $config) : Components\Components
    {
        $components = Annotations\BindingsBuilders::instance();
        $this->components = &$components;

        $consumerBuilders = array_merge([
            $config->providersConsumer(
                $this->configureProviders(Consumers::buildWithDefaultDataModel())
            ),
            $config->controllersConsumer(
                $this->configureControllers(Consumers::buildWithDefaultDataModel())
            ),
            $config->factoriesConsumer(
                $this->configureFactories(Consumers::buildWithDefaultDataModel())
            ),
            $config->convertersConsumer(
                $this->configureConverters(Consumers::buildWithDefaultDataModel())
            ),
            $config->parametersConsumer(
                $this->configureParameters(Consumers::buildWithDefaultDataModel())
            ),
        ], $this->keys());
        $consumerBuilders = array_filter($consumerBuilders, function ($x) {
            return !!$x;
        });
        $consumers = array_map(function (ConsumerBuilder $builder) {
            return $builder->build();
        }, $consumerBuilders);

        Streams::classFiles($config->getFolders())->consume(new ClassFileConsumer($consumers));

        $result = $components->build();
        $this->components = null;

        return $result;
    }

    /**
     * @param array | string[] $sources
     * @return Components\Components
     * @throws \Exception
     */
    public function scan(array $sources) : Components\Components
    {
        $config = ScanConfigDefault::instance($sources);
        return $this->scanWithConfig($config);
    }
}
