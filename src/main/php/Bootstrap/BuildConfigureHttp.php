<?php namespace Motorphp\SilexTools\Bootstrap;

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
            ->withServiceHandler($reflection)
        ;

        return $this;
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