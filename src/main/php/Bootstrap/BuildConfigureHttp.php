<?php namespace Motorphp\SilexTools\Bootstrap;

use Motorphp\SilexAnnotations\Common\ParamConverter;
use Motorphp\SilexAnnotations\Common\Service;
use Motorphp\SilexTools\NetteLibrary\MethodBody;
use Nette\PhpGenerator\Factory;
use Nette\PhpGenerator\Method;
use Swagger\Annotations\Operation;

class BuildConfigureHttp
{
    /** @var string */
    private $containerArg;

    /** @var string */
    private $controllerFactoryArg;

    /** @var \ReflectionMethod */
    private $method;

    /** @var BuildContext */
    private $context;

    /**
     * @var array| DeclarationRoute[]
     */
    private $routes = [];

    /**
     * @var array| EntryConverter[]
     */
    private $converters = [];

    /**
     * @var array| EntryParam[]
     */
    private $routeParams = [];

    /**
     * @var array| EntryCallback[]
     */
    private $callbacks = [];

    /**
     * BuildConfigureHttp constructor.
     * @param string $containerArg
     * @param string $controllerFactoryArg
     * @param \ReflectionMethod $method
     * @param BuildContext $context
     */
    public function __construct(string $containerArg, string $controllerFactoryArg, \ReflectionMethod $method, BuildContext $context)
    {
        $this->containerArg = $containerArg;
        $this->controllerFactoryArg = $controllerFactoryArg;
        $this->method = $method;
        $this->context = $context;
    }

    public function buildClass() : BuildBootstrap
    {
        $context = $this->build();
        return new BuildBootstrap($context);
    }

    private function build()
    {
        $declarations = array_filter($this->routes, function (DeclarationRoute $a) {
            return $a->canBuild();
        });

        foreach ($this->callbacks as $entryCallback) {
            $serviceKey = $entryCallback->serviceKey;
            $name = $this->context->getFirstName($serviceKey);
            if ($name) {
                $callback = $entryCallback->callback;
                $callback->withServiceKeyFromConstant($name);
            }
        }

        foreach ($declarations as $routeKey => $declaration) {
            $converters = $this->matchConverters($routeKey);
            if (!empty($converters)) {
                $declaration->withAllParamConverter($converters);
            }
        }

        $parts = array_map(function (DeclarationRoute $a) {
            return $a->build();
        }, $declarations);

        $methodBody = new MethodBody($parts);
        $method = (new Factory)->fromMethodReflection($this->method);
        $methodBody->configure($method);

        $context = clone $this->context;
        $context->addMethod($method);
        $methodBody->addAllImports($context);

        return $context;
    }

    private function matchConverters(string $routeKey) : array
    {
        $params = array_filter($this->routeParams, function (EntryParam $entry) use ($routeKey) {
            return $entry->routeKey === $routeKey;
        });

        /** @var ServiceCallback[] $matched */
        $matched = [];
        foreach ($params as $param) {
            /** @var EntryConverter[] $matchCandidates */
            $matchCandidates = [];

            foreach ($this->converters as $converter) {
                $typeMatch = $param->type === $converter->type;
                $operationMatch = empty($converter->operationId) || $param->operationId === $converter->operationId;
                if ($typeMatch && $operationMatch) {
                    $matchCandidates[] = $converter;
                }
            }

            /** EntryConverter */
            $candidate = null;

            if (1 == count($matchCandidates)) {
                $candidate = array_pop($matchCandidates);
            } else if (1 < count($matchCandidates)) {
                $specific = array_filter($matchCandidates, function (EntryConverter $converter) {
                    return !empty($converter->operationId);
                });

                if (1 === count($specific)) {
                    $candidate = array_pop($specific);
                } else if (0 == count($specific)) {
                    throw new \DomainException('two many generic converters for a single type');
                } else {
                    throw new \DomainException('two many operation converters for a single type');
                }
            }

            if ($candidate) {
                $matched[$param->name] = $candidate->callback;
            }
        }

        return $matched;
    }

    public function addAllControllers(array $reflections): BuildConfigureHttp
    {
        foreach ($reflections as $reflection) {
            $this->addController($reflection);
        }

        return $this;
    }

    public function addController(\ReflectionMethod $reflection) : BuildConfigureHttp
    {
        $reader = $this->context->getAnnotationsReader();

        $annotations = $reader->getMethodAnnotations($reflection);
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
            ->withController($reflection)
        ;

        foreach ($reflection->getParameters() as $parameter) {
            $entryParam = new EntryParam();
            $entryParam->type = $parameter->getType()->getName();
            $entryParam->operationId = $operation->operationId;
            $entryParam->name = $parameter->getName();
            $entryParam->routeKey = $key;

            $this->routeParams[] = $entryParam;
        }

        return $this;
    }

    public function addAllConverters(array $reflections): BuildConfigureHttp
    {
        foreach ($reflections as $reflection) {
            $this->addConverter($reflection);
        }

        return $this;
    }

    public function addConverter(\ReflectionMethod $reflection) : BuildConfigureHttp
    {
        $reader = $this->context->getAnnotationsReader();
        /** @var ParamConverter $annotation */
        $annotation = $reader->getMethodAnnotation($reflection, ParamConverter::class);

        $callback = ServiceCallback::fromMethod($reflection);
        $serviceKey = $this->setServiceKey($callback, $reflection);

        $entryCallback = new EntryCallback();
        $entryCallback->callback = $callback;
        $entryCallback->serviceKey = $serviceKey;

        $entry = new EntryConverter();
        $entry->callback = $callback;
        $entry->type = $reflection->getReturnType()->getName();
        $entry->operationId = $annotation->operation;

        $this->converters[] = $entry;
        return $this;
    }

    private function setServiceKey(ServiceCallback $callback, \ReflectionMethod $reflection)
    {
        $reader = $this->context->getAnnotationsReader();
        /** @var ParamConverter $annotation */
        $annotation = $reader->getMethodAnnotation($reflection, ParamConverter::class);

        if ($annotation->service) {
            $callback->withServiceKey($annotation->service);
            return $annotation->service;
        }

        $class = $reflection->getDeclaringClass();
        /** @var Service $serviceAnnotation */
        $serviceAnnotation = $reader->getClassAnnotation($class, Service::class);
        if ($serviceAnnotation && $serviceAnnotation->name) {
            $callback->withServiceKey($serviceAnnotation->name);
            return $serviceAnnotation->name;
        }

        $callback->withServiceKeyFromClass($class);
        return $class->getName();
    }

    private function getOrCreateDeclarationRouteBuilder(string $key): DeclarationRoute
    {
        if (! array_key_exists($key, $this->routes)) {
            $declaration = new DeclarationRoute();
            $this->routes[$key] = $declaration;
            return $declaration;
        }

        return $this->routes[$key];
    }
}