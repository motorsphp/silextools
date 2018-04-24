<?php namespace Motorphp\SilexTools\Bootstrap;

use Motorphp\SilexAnnotations\Common\ContainerKey;
use Motorphp\SilexAnnotations\Common\ServiceFactory;
use Motorphp\SilexAnnotations\Reader\ConstantsReader;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Silex\ControllerCollection;
use Swagger\Annotations\Operation;

class BootstrapBuilder
{
    /**
     * @var ConstantsReader
     */
    private $reader;

    /**
     * @var array|DeclarationServiceFactory[]
     */
    private $factories = [];

    /**
     * @var BootstrapMethodBuilder
     */
    private $configureFactories;

    /**
     * @var array| DeclarationProvider[]
     */
    private $providers = [];

    /**
     * @var BootstrapMethodBuilder
     */
    private $configureProviders;

    /**
     * @var array| DeclarationRoute[]
     */
    private $routes = [];

    /**
     * @var BootstrapMethodBuilder
     */
    private $configureHttp;

    /**
     * @param string $type
     * @return BootstrapBuilder
     * @throws \ReflectionException
     */
    public static function withContainerType(ConstantsReader $reader, string $type) : BootstrapBuilder
    {
        $reflection = new \ReflectionClass($type);
        return new BootstrapBuilder(
            $reader,
            BootstrapMethodBuilder::configureFactories()->setArgContainerType($reflection),
            BootstrapMethodBuilder::configureProviders()->setArgContainerType($reflection),
            BootstrapMethodBuilder::configureHttp()->setArgContainerType($reflection)
        );
    }

    public function __construct(
        ConstantsReader $reader,
        BootstrapMethodBuilder $configureFactories,
        BootstrapMethodBuilder $configureProviders,
        BootstrapMethodBuilder $configureHttp
    ) {
        $this->reader = $reader;

        $this->configureFactories = $configureFactories;
        $this->configureProviders = $configureProviders;
        $this->configureHttp = $configureHttp;
    }

    private function getOrCreateDeclarationRouteBuilder(string $key): DeclarationRoute
    {
        if (! array_key_exists($key, $this->routes)) {
            $declaration = new DeclarationRoute();
            $this->routes[$key] = $declaration;
            return $declaration;
        }

        return $this->factories[$key];
    }

    private function getOrCreateServiceDeclarationBuilder(string $key): DeclarationServiceFactory
    {
        if (! array_key_exists($key, $this->factories)) {
            $declaration = new DeclarationServiceFactory();
            $this->factories[$key] = $declaration;
            return $declaration;
        }

        return $this->factories[$key];
    }

    public function addProvider(\ReflectionClass $provider) : BootstrapBuilder
    {
        $declaration = new DeclarationProvider();
        $declaration->setProviderFromClass($provider);
        $this->providers[] = $declaration;

        return $this;
    }

    public function addFactoryKey(\ReflectionClassConstant $reflection)
    {
        $correlationKey = null;

        /** @var ContainerKey $annotation */
        $annotation = $this->reader->getConstantAnnotation($reflection, ContainerKey::class);
        if (!empty($annotation->for)) {
            $correlationKey = $annotation->for;
        }

        if (empty($correlationKey)) {
            $correlationKey = $reflection->getDeclaringClass()->getName();
        }

        if (empty($correlationKey)) {
            throw new \RuntimeException('key is empty');
        }

        $builder = $this->getOrCreateServiceDeclarationBuilder($correlationKey);
        $builder->addKeyFromConstant($reflection);

        return $builder;
    }

    public function addServiceFactory(\ReflectionMethod $reflection) : DeclarationServiceFactory
    {
        /** @var ServiceFactory $annotation */
        $annotation = $this->reader->getMethodAnnotation($reflection, ServiceFactory::class);

        $correlationKey = $annotation->containerKey;
        if (empty($correlationKey)) { // infer the container key from the return type
            $returnType = $reflection->getReturnType();
            if (!is_null($returnType)) {
                $correlationKey = $returnType->getName();
            }
        }

        if (empty($correlationKey)) {
            throw new \DomainException('could not infer the container key');
        }

        $builder = $this->getOrCreateServiceDeclarationBuilder($correlationKey);
        $builder->addFactoryFromMethod($reflection);

        return $builder;
    }

    public function addController(\ReflectionMethod $reflection): DeclarationRoute
    {
        $annotations = $this->reader->getMethodAnnotations($reflection);
        /** @var Operation[] $operations */
        $operations = array_filter($annotations, function ($o) {
            return $o instanceof Operation;
        });
        if (count($operations) !== 1) {
            throw new \RuntimeException('two many operations');
        }
        $operation = array_pop($operations);

        $key = $operation->method . $operation->path;
        $builder = $this->getOrCreateDeclarationRouteBuilder($key);
        $builder
            ->withHttpPath($operation->path)
            ->withHttpMethod($operation->method)
            ->withServiceHandler($reflection)
        ;

        return $builder;

    }

    public function addParamConverter(\ReflectionMethod $reflection)
    {

    }

    /**
     * @param string $name
     * @param string $type
     * @return BootstrapBuilder
     * @throws \ReflectionException
     */
    public function addConfigureHttpArg(string $name, string $type) : BootstrapBuilder
    {
        $this->configureHttp->addArgumentFromString($name, $type);
        return $this;
    }

    public function build(): string
    {
        $namespace = new PhpNamespace('Bootstrap');
        $class = new ClassType('Bootstrap', $namespace);

        $methodBuilder = $this->buildMethod($this->factories, $this->configureFactories);
        $methodBuilder->build($class);

        $methodBuilder = $this->buildMethod($this->providers, $this->configureProviders);
        $methodBuilder->build($class);

        $methodBuilder = $this->buildMethod($this->routes, $this->configureHttp);
        $methodBuilder->addArgumentFromString('controllers',ControllerCollection::class);
        $methodBuilder->build($class);

        return '<?php ' . (string) $namespace . (string) $class;
    }

    /**
     * @param array|Declaration[] $declarations
     * @param BootstrapMethodBuilder $methodBuilder
     * @return BootstrapMethodBuilder
     */
    private function buildMethod(array $declarations, BootstrapMethodBuilder $methodBuilder): BootstrapMethodBuilder
    {
        $declarations = array_filter($declarations, function(Declaration $declaration) {
            return $declaration->canBuild();
        });

        $reducer = function(BootstrapMethodBuilder $builder, Declaration $declaration) {
            $declaration->build($builder);
            return $builder;
        };

        /** @var BootstrapMethodBuilder $methodBuilder */
        return array_reduce($declarations, $reducer, $methodBuilder);
    }
}