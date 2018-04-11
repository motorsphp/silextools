<?php namespace Motorphp\SilexTools\Bootstrap;

use Motorphp\SilexAnnotations\Common\ContainerKey;
use Motorphp\SilexAnnotations\Common\ServiceFactory;
use Motorphp\SilexAnnotations\Reader\ConstantsReader;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;

class BootstrapBuilder
{
    /**
     * @var \ReflectionClass
     */
    private $containerType;

    /**
     * @var ConstantsReader
     */
    private $reader;

    /**
     * @var array|DeclarationServiceFactory[]
     */
    private $factories = [];

    /**
     * @var array| DeclarationProvider[]
     */
    private $providers = [];

    /**
     * @param string $type
     * @return BootstrapBuilder
     * @throws \ReflectionException
     */
    public static function withContainerType(ConstantsReader $reader, string $type) : BootstrapBuilder
    {
        $reflection = new \ReflectionClass($type);
        return new BootstrapBuilder($reader, $reflection);
    }

    public function __construct(ConstantsReader $reader, \ReflectionClass $containerType)
    {
        $this->reader = $reader;
        $this->containerType = $containerType;
    }


    private function getOrCreateDeclarationBuilder(string $key): DeclarationServiceFactory
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

        $builder = $this->getOrCreateDeclarationBuilder($correlationKey);
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

        $builder = $this->getOrCreateDeclarationBuilder($correlationKey);
        $builder->addFactoryFromMethod($reflection);

        return $builder;
    }

    public function build(): string
    {
        $namespace = new PhpNamespace('Bootstrap');
        $class = new ClassType('Bootstrap', $namespace);

        $methodBuilder = $this->buildConfigureFactories();
        $methodBuilder->setArgContainerType($this->containerType);
        $methodBuilder->build($class);

        $methodBuilder = $this->buildConfigureProviders();
        $methodBuilder->setArgContainerType($this->containerType);
        $methodBuilder->build($class);

        return '<?php ' . (string) $namespace . (string) $class;

    }

    private function buildConfigureFactories() : BootstrapMethodBuilder
    {
        $declarations = array_filter($this->factories, function(DeclarationServiceFactory $declaration) {
            return $declaration->canBuild();
        });

        $reducer = function(BootstrapMethodBuilder $builder, DeclarationServiceFactory $declaration) {
            $declaration->build($builder);
            return $builder;
        };
        /** @var BootstrapMethodBuilder $methodBuilder */
        $methodBuilder = array_reduce($declarations, $reducer, BootstrapMethodBuilder::configureFactories());
        return $methodBuilder;
    }

    private function buildConfigureProviders() : BootstrapMethodBuilder
    {
        $declarations = array_filter($this->providers, function(DeclarationProvider $declaration) {
            return $declaration->canBuild();
        });

        $reducer = function(BootstrapMethodBuilder $builder, DeclarationProvider $declaration) {
            $declaration->build($builder);
            return $builder;
        };
        /** @var BootstrapMethodBuilder $methodBuilder */
        $methodBuilder = array_reduce($declarations, $reducer, BootstrapMethodBuilder::configureProviders());
        return $methodBuilder;
    }
}