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
     * @var array|DeclarationFactory[]
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
        $declarations = array_filter($this->factories, function (DeclarationFactory $a) {
            return $a->canBuild();
        });

        foreach ($declarations as $serviceKey => $declaration) {
            $name = $this->context->getFirstName($serviceKey);
            if ($name) {
                $declaration->withKeyFromConstant($name);
            }
        }

        $parts = array_map(function (DeclarationFactory $a) {
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
        $serviceKey = null;
        $reader = $this->context->getAnnotationsReader();

        /** @var ContainerKey $annotation */
        $annotation = $reader->getConstantAnnotation($reflection, ContainerKey::class);
        if (empty($annotation->service)) {
            $serviceKey = $reflection->getDeclaringClass()->getName();
        } else {
            $serviceKey = $annotation->service;
        }

        $this->context->addName($serviceKey, $reflection);
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

        $declaration = new DeclarationFactory();

        $serviceKey = $annotation->service;
        if (empty($serviceKey)) { // infer the container key from the return type
            $returnType = $reflection->getReturnType();
            if (!is_null($returnType)) {
                $serviceKey = $returnType->getName();
                if (!$returnType->isBuiltin()) {
                    try {
                        $reflectionClass = new \ReflectionClass($serviceKey);
                        $declaration->withKeyFromClass($reflectionClass);
                    } catch (\ReflectionException $e) {
                        $declaration->withKeyFromString($serviceKey);
                    }
                }
            }
        } else {
            $declaration->withKeyFromString($serviceKey);
        }

        if (empty($serviceKey)) {
            throw new \DomainException('could not infer the container key');
        }

        $declaration->withFactoryFromMethod($reflection);
        $this->factories[$serviceKey] = $declaration;

        return $this;
    }

}