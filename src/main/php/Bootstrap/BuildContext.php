<?php namespace Motorphp\SilexTools\Bootstrap;

use Motorphp\SilexAnnotations\Reader\ConstantsReader;
use Nette\PhpGenerator\Method;

class BuildContext
{
    /** @var ConstantsReader  */
    private $reader;

    /** @var array|Method[] */
    private $methods = [];

    /** @var array|\ReflectionClass[]  */
    private $imports = [];

    /** @var array|\ReflectionClassConstant[]  */
    private $names = [];

    public function __construct(ConstantsReader $reader)
    {
        $this->reader = $reader;
    }

    public function getFirstName(string $name) : ?\ReflectionClassConstant
    {
        $entry = null;
        if (array_key_exists($name, $this->names)) {
            $entry = $this->names[$name];
        }

        if (is_array($entry)) {
            return $entry[0];
        }

        return $entry;
    }

    public function addName(string $name, \ReflectionClassConstant $constant) : BuildContext
    {
        if (array_key_exists($name, $this->names)) {
            $entry = $this->names[$name];
            if (is_array($entry)) {
                $entry[] = $constant;
            } else {
                $this->names[$name] = [$entry, $constant];
            }
        } else {
            $this->names[$name] = $constant;
        }

        return $this;
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