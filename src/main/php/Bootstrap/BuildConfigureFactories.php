<?php namespace Motorphp\SilexTools\Bootstrap;

use Motorphp\SilexAnnotations\Common\ContainerKey;
use Motorphp\SilexAnnotations\Common\ServiceFactory;
use Motorphp\SilexTools\NetteLibrary\MethodBody;
use Nette\PhpGenerator\Factory;

class BuildConfigureFactories
{
    /** @var \ReflectionMethod */
    private $method;

    /** @var BuildContext */
    private $context;

    /**
     * @var array|DeclarationServiceFactory[]
     */
    private $factories = [];

    public function __construct(\ReflectionMethod $method, BuildContext $context)
    {
        $this->method = $method;
        $this->context = $context;
    }

    /**
     * @param string $class
     * @return BuildConfigureProviders
     * @throws \ReflectionException
     */
    public function configureProviders(string $class)
    {
        $context = $this->build();
        return Builders::configureProviders($class, $context);
    }

    /**
     * @param string $class
     * @return BuildConfigureHttp
     * @throws \ReflectionException
     */
    public function configureHttp(string $class) : BuildConfigureHttp
    {
        $context = $this->build();
        return Builders::configureHttp($class, $context);
    }

    public function buildClass() : BuildBootstrap
    {
        $context = $this->build();
        return new BuildBootstrap($context);
    }

    private function build() : BuildContext
    {
        $declarations = array_filter($this->factories, function (DeclarationServiceFactory $a) {
            return $a->canBuild();
        });
        $parts = array_map(function (DeclarationServiceFactory $a) {
            return $a->build();
        }, $declarations);

        $methodBody = new MethodBody($parts);
        $method = (new Factory)->fromMethodReflection($this->method);

        $context = clone $this->context;
        $methodBody->addAllImports($context);
        $methodBody->configure($method);
        $context->addMethod($method);

        return $context;
    }

    public function addAllFactoryKeys(array $reflections): BuildConfigureFactories
    {
        foreach ($reflections as $reflection) {
            $this->addFactoryKey($reflection);
        }

        return $this;
    }

    public function addFactoryKey(\ReflectionClassConstant $reflection) : BuildConfigureFactories
    {
        $correlationKey = null;
        $reader = $this->context->getAnnotationsReader();
        /** @var ContainerKey $annotation */
        $annotation = $reader->getConstantAnnotation($reflection, ContainerKey::class);
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

        return $this;
    }

    public function addAllServiceFactories(array $reflections): BuildConfigureFactories
    {
        foreach ($reflections as $reflection) {
            $this->addServiceFactory($reflection);
        }

        return $this;
    }

    public function addServiceFactory(\ReflectionMethod $reflection): BuildConfigureFactories
    {
        $reader = $this->context->getAnnotationsReader();
        /** @var ServiceFactory $annotation */
        $annotation = $reader->getMethodAnnotation($reflection, ServiceFactory::class);

        $serviceKey = $annotation->service;
        $correlationKey = $annotation->service;
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

        if (! empty($serviceKey)) {
            $builder->addKeyFromString($serviceKey);
        }

        return $this;
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
}