<?php namespace Motorphp\SilexTools\Bootstrap;

use Motorphp\SilexAnnotations\Reader\ConstantsReader;
use Motorphp\SilexTools\NetteLibrary\MethodBody;
use Motorphp\SilexTools\NetteLibrary\MethodBodyPart;
use Nette\PhpGenerator\Factory;
use Nette\PhpGenerator\Method;

class BuildConfigureProviders
{
    /**
     * @var array| DeclarationRoute[]
     */
    private $routes = [];

    /** @var \ReflectionMethod */
    private $method;

    /** @var BuildContext */
    private $context;

    /**
     * @var array| DeclarationProvider[]
     */
    private $declarations = [];

    public function __construct(\ReflectionMethod $method, BuildContext $context)
    {
        $this->method = $method;
        $this->context = $context;
    }

    /**
     * @param string $class
     * @return BuildConfigureHttp
     * @throws \ReflectionException
     */
    public function configureHttp(string $class): BuildConfigureHttp
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
        $context = clone $this->context;

        $methodBody = new MethodBody($this->declarations);
        $method = (new Factory)->fromMethodReflection($this->method);

        $methodBody->addAllImports($context);
        $methodBody->configure($method);
        $context->addMethod($method);

        return $context;
    }

    public function addAllProviders(array $reflections): BuildConfigureProviders
    {
        foreach ($reflections as $reflection) {
            $this->addProvider($reflection);
        }

        return $this;
    }

    public function addProvider(\ReflectionClass $provider) : BuildConfigureProviders
    {
        $declaration = new DeclarationProvider();
        $declaration->setProviderFromClass($provider);
        $this->declarations[] = $declaration->build();

        return $this;
    }
}