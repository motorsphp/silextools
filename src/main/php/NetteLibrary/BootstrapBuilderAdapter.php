<?php namespace Motorphp\SilexTools\NetteLibrary;

use Motorphp\SilexTools\Bootstrap\BootstrapBuilder;
use Motorphp\SilexTools\Bootstrap\MethodBuilder;
use Motorphp\SilexTools\NetteLibrary\FactoryAdapters;
use Motorphp\SilexTools\NetteLibrary\Method\MethodBody;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Factory;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpNamespace;

class BootstrapBuilderAdapter implements BootstrapBuilder
{
    /** @var string */
    private $name;

    /** @var string */
    private $namespace;

    /** @var $array|Method[] */
    private $methods = [];

    /** @var $array|string[] */
    private $imports = [];

    /**
     * @param string $class
     * @return BootstrapBuilder
     * @throws \ReflectionException
     */
    public function withSameNamespaceAsClass(string $class) : BootstrapBuilder
    {
        $reflection = new \ReflectionClass($class);
        $this->namespace = $reflection->getNamespaceName();
        return $this;
    }

    public function withNamespace(string $namespace) : BootstrapBuilder
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * @param string $class
     * @return BootstrapBuilder
     * @throws \ReflectionException
     */
    public function withClassname(string $class) : BootstrapBuilder
    {
        $reflection = new \ReflectionClass($class);
        $this->name = $reflection->getShortName();
        return $this;
    }

    public function withMethodBody(MethodBody $body, \ReflectionMethod $signature) : BootstrapBuilder
    {
        $method = (new Factory)->fromMethodReflection($signature);
        $body->configure($method);
        $this->withMethod($method);

        $imports = $body->getImports();
        $this->imports = array_merge($this->imports, $imports);

        return $this;
    }

    public function withMethod(Method $method)
    {
        $this->methods[] = $method;
        return $this;
    }

    public function withRoutes(\ReflectionMethod $signature) : MethodBuilder
    {
        $builder = new RouteAdapters\MethodBuilderAdapter($this);
        $builder->withSignature($signature);

        return $builder;
    }

    public function withProviders(\ReflectionMethod $signature): MethodBuilder
    {
        $builder = new ProviderAdapters\MethodBuilderAdapter($this);
        $builder->withSignature($signature);

        return $builder;
    }

    public function withFactories(\ReflectionMethod $signature): MethodBuilder
    {
        $builder = new FactoryAdapters\MethodBuilderAdapter($this);
        $builder->withSignature($signature);

        return $builder;
    }

    public function build() : string
    {
        $namespace = new PhpNamespace($this->namespace);
        $imports = array_unique($this->imports);

        foreach ($imports as $import) {
            $namespace->addUse($import);
        }

        $class = new ClassType($this->name, $namespace);
        $class->setMethods($this->methods);

        return '<?php ' . (string) $namespace . (string) $class;
    }

}