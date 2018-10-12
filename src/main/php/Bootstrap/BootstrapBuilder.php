<?php namespace Motorphp\SilexTools\Bootstrap;

use Motorphp\SilexAnnotations\Reader\ConstantsReader;
use Motorphp\SilexTools\Annotations\ComponentsScanner;
use Motorphp\SilexTools\Components\Components;
use Motorphp\SilexTools\NetteLibrary\BootstrapWritter;

class BootstrapBuilder
{
    /** @var string */
    private $namespace;

    /** @var string */
    private $classname;

    /** @var string|string[]$folders */
    private $folders;

    /** @var \Closure */
    private $configurator;

    /**
     * BootstrapBuilder constructor.
     * @param \Closure $configurator
     */
    public function __construct( \Closure $configurator )
    {
        $this->configurator = $configurator;
    }

    /**
     * @param string $class
     * @return BootstrapBuilder
     * @throws \ReflectionException
     */
    public function withNamespaceAndClassname( string $class) : BootstrapBuilder
    {
        $this->namespace = $class;
        $this->classname = $class;
        return $this;
    }

    /**
     * @param string $namespace
     * @return BootstrapBuilder
     */
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
        $this->classname = $class;
        return $this;
    }

    /**
     * @param string|string[]$folders
     * @return BootstrapBuilder
     */
    public function withComponentsFrom($folders) : BootstrapBuilder
    {
        $this->folders = is_array($folders) ? $folders : [$folders];
        return $this;
    }

    /**
     * @return string
     * @throws \ReflectionException
     * @throws \Exception
     */
    function build(): string
    {
        $writter = new BootstrapWritter();

        if ($this->namespace === $this->classname && $this->namespace) {
            $reflection = new \ReflectionClass($this->namespace);

            $writter
                ->withNamespace($reflection->getNamespaceName())
                ->withClassname($this->namespace)
            ;
        } else {
            $writter->withClassname($this->classname);
            if ($this->namespace) {
                $writter->withNamespace($this->namespace);
            }
        }

        $components = ComponentsScanner::createDefault(ConstantsReader::instance())->scan($this->folders);
        $writter->withComponents($components);

        $configurator = $this->configurator;
        $configurator($writter);

        return $writter->build();
    }

}
