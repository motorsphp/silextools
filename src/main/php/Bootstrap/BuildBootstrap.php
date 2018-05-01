<?php namespace Motorphp\SilexTools\Bootstrap;

use Nette\InvalidStateException;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;

class BuildBootstrap
{
    /** @var BuildContext */
    private $context;

    /** @var string */
    private $namespace = 'Bootstrap';

    /** @var string */
    private $name = 'Bootstrap';

    public function __construct(BuildContext $context)
    {
        $this->context = $context;
    }

    /**
     * @param string $class
     * @return BuildBootstrap
     * @throws \ReflectionException
     */
    public function withSameNamespaceAs(string $class) : BuildBootstrap
    {
        $reflection = new \ReflectionClass($class);
        $this->namespace = $reflection->getNamespaceName();
        return $this;
    }

    public function withNamespace(string $namespace) : BuildBootstrap
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * @param string $class
     * @return BuildBootstrap
     * @throws \ReflectionException
     */
    public function withSameNameAs(string $class) : BuildBootstrap
    {
        $reflection = new \ReflectionClass($class);
        $this->name = $reflection->getShortName();
        return $this;
    }

    public function withName(string $name) : BuildBootstrap
    {
        $this->name = $name;
        return $this;
    }

    public function sameAs(string $class): BuildBootstrap
    {
        $reflection = new \ReflectionClass($class);
        $this->namespace = $reflection->getNamespaceName();
        $this->name = $reflection->getShortName();

        return $this;
    }

    public function build(): string
    {
        $namespace = new PhpNamespace($this->namespace);
        $this->buildImports($namespace);

        $class = new ClassType($this->name, $namespace);
        $methods = $this->context->getMethods();
        $class->setMethods($methods);

        return '<?php ' . (string) $namespace . (string) $class;
    }

    private function buildImports(PhpNamespace $namespace)
    {
        $imports = $this->context->getImports();
        $imports = array_unique($imports);

        foreach ($imports as $import) {
            try {
                $namespace->addUse($import);
            } catch (InvalidStateException $e) {
                /** ignore */
            }
        }
    }
}