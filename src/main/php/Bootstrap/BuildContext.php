<?php namespace Motorphp\SilexTools\Bootstrap;

use Motorphp\SilexAnnotations\Reader\ConstantsReader;
use Nette\PhpGenerator\Method;

class BuildContext
{
    private $methods = [];

    private $imports = [];

    /** @var ConstantsReader  */
    private $reader;

    public function __construct(ConstantsReader $reader)
    {
        $this->reader = $reader;
    }

    public function withMethod(Method $method) : BuildContext
    {
        $context = clone $this;
        $context->methods[] = $method;

        return $context;
    }

    public function addMethod(Method $method) : BuildContext
    {
        $this->methods[] = $method;
        return $this;
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @return array|string[]
     */
    public function getImports() : array
    {
        return $this->imports;
    }

    public function addImport(\ReflectionClass $class) : BuildContext
    {
        $this->imports[] = $class->getName();
        return $this;
    }

    /**
     * @param array|\ReflectionClass[] $classes
     * @return $this
     */
    public function addAllImports(array $classes) : BuildContext
    {
        $mapper = function (\ReflectionClass $class) { return $class->getName(); };
        $imports = array_map($mapper, $classes);

        $this->imports = array_merge($this->imports, $imports);
        return $this;
    }

    /**
     * @param array|\ReflectionClass[] $classes
     * @return BuildContext
     */
    public function withAllImports(array $classes)
    {
        $context = clone $this;
        $context->addAllImports($classes);

        return $context;
    }

    /**
     * @return ConstantsReader
     */
    public function getAnnotationsReader(): ConstantsReader
    {
        return $this->reader;
    }


}