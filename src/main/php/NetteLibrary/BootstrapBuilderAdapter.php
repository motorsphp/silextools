<?php namespace Motorphp\SilexTools\NetteLibrary;

use Motorphp\SilexAnnotations\Reader\ConstantsReader;
use Motorphp\SilexTools\Annotations\ComponentsScanner;
use Motorphp\SilexTools\Bootstrap\BootstrapBuilder;
use Motorphp\SilexTools\Components\Components;
use Motorphp\SilexTools\Components\ComponentsVisitorGroup;
use Motorphp\SilexTools\NetteLibrary\Method\BodyWriter;
use Motorphp\SilexTools\NetteLibrary\Method\Configuration;
use Motorphp\SilexTools\NetteLibrary\Method\MethodBody;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Factory;
use Nette\PhpGenerator\PhpNamespace;

class BootstrapBuilderAdapter implements BootstrapBuilder
{
    /** @var string */
    private $name;

    /** @var string */
    private $namespace;

    /** @var $array|Configuration[] */
    private $methods = [];

    /** @var $array|string[] */
    private $imports = [];

    /** @var Components */
    private $components;

    /** @var array|string[] */
    private $folders;

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

    public function withComponents(Components $components) : BootstrapBuilder
    {
        $this->components = $components;
        $this->folders = null;
        return $this;
    }

    /**
     * @param string|string[] $folders
     * @return BootstrapBuilder
     */
    public function withComponentsFrom($folders): BootstrapBuilder
    {
        if (is_string($folders)) {
            $this->folders = [ $folders ];
        } else if (is_array($folders)){
            $this->folders = $folders;
        } else {
            throw new \InvalidArgumentException('expecting either a path to a folder or a list of folders');
        }

        $this->components = null;
        return $this;
    }

    public function withMethod(BodyWriter $writer, \ReflectionMethod $signature)  : BootstrapBuilder
    {
        $configuration = new Configuration();
        $configuration->setWriter($writer);
        $configuration->setSignature($signature);

        $this->methods[] = $configuration;
        return $this;
    }

    public function build() : string
    {
        if (! empty($this->folders)) {
            $components =  ComponentsScanner::createDefault(ConstantsReader::instance())->scan($this->folders);
        } else {
            $components = &$this->components;
        }

        $writers = array_map(function (Configuration $configuration) {
            return $configuration->getWriter();
        }, $this->methods);
        $visitor = new ComponentsVisitorGroup($writers);
        $components->visit($visitor);

        $imports = array_merge([], $this->imports);
        $methods = [];
        foreach ($this->methods as $configuration) {

            $writer = $configuration->getWriter();
            $signature = $configuration->getSignature();

            $method = (new Factory)->fromMethodReflection($signature);
            $methods[] = $method;
            /** @var MethodBody $body */
            $body = $writer->getMethodBody();
            $body->configure($method);

            $imports = array_merge($imports, $body->getImports());
        }

        $imports = array_unique($imports);
        $namespace = new PhpNamespace($this->namespace);
        foreach ($imports as $import) {
            $namespace->addUse($import);
        }

        $class = new ClassType($this->name, $namespace);
        $class->setMethods($methods);

        return '<?php ' . (string) $namespace . (string) $class;
    }


}