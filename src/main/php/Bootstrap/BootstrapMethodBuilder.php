<?php namespace Motorphp\SilexTools\Bootstrap;

use Motorphp\SilexTools\NetteLibrary\MethodBodyPart;
use Nette\InvalidStateException;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpNamespace;

class BootstrapMethodBuilder
{
    /**
     * @var \ReflectionClass
     */
    private $argContainerType;

    /**
     * @var string
     */
    private $methodName;

    /**
     * @var array| string[]
     */
    private $imports = [];

    /**
     * @var array| MethodBodyPart[]
     */
    private $methodBody = [];

    public static function configureProviders() : BootstrapMethodBuilder
    {
        $builder = new BootstrapMethodBuilder();
        $builder->setMethodName(__FUNCTION__);

        return $builder;
    }

    public static function configureFactories() : BootstrapMethodBuilder
    {
        $builder = new BootstrapMethodBuilder();
        $builder->setMethodName(__FUNCTION__);

        return $builder;
    }

    public function setMethodName(string $name) : BootstrapMethodBuilder
    {
        $this->methodName = $name;
        return $this;
    }

    public function setArgContainerType(\ReflectionClass $reflection): BootstrapMethodBuilder
    {
        $this->argContainerType = $reflection;
        return $this;
    }

    /**
     * @param array|string[] $imports
     * @return BootstrapMethodBuilder
     */
    public function addImports(array $imports): BootstrapMethodBuilder
    {
        $this->imports = array_merge($this->imports, $imports);
        return $this;
    }

    public function addImportForConstant(\ReflectionClassConstant $class): BootstrapMethodBuilder
    {
        $import = $class->getDeclaringClass()->getName();
        $this->imports[] = $import;

        return $this;
    }

    public function addImportForMethod(\ReflectionMethod $method): BootstrapMethodBuilder
    {
        $import = $method->getDeclaringClass()->getName();
        $this->imports[] = $import;

        return $this;
    }

    public function addImportForClass(\ReflectionClass $class): BootstrapMethodBuilder
    {
        $this->imports[] = $class->getName();
        return $this;
    }

    public function addMethodBody(MethodBodyPart $bodyPart) : BootstrapMethodBuilder
    {
        $this->methodBody[] = $bodyPart;
        return $this;
    }

    public function build(ClassType $class)
    {
        $namespace = $class->getNamespace();
        $this->buildImports($namespace);

        $method = $this->buildMethodSignature($class);
        $this->buildMethodBody($method);
    }

    private function buildMethodSignature(ClassType $class) : Method
    {
        $method = $class->addMethod($this->methodName);
        $method
            ->addParameter('container')
            ->setTypeHint($this->argContainerType->getShortName())
        ;

        return $method;
    }

    private function buildMethodBody(Method $method)
    {
        /** @var MethodBodyPart $body */
        $body = array_reduce($this->methodBody, function (MethodBodyPart $carry, MethodBodyPart $item) {
            return $carry->merge($item);
        }, new MethodBodyPart('', []));

        $body->configure($method);
    }

    private function buildImports(PhpNamespace $namespace)
    {
        $imports = array_unique($this->imports);
        $imports[] = $this->argContainerType->getName();

        foreach ($imports as $import) {
            try {
                $namespace->addUse($import);
            } catch (InvalidStateException $e) {
                /** ignore */
            }
        }
    }
}
