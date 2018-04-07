<?php namespace Motorphp\SilexTools\ClassScanner;

class ClassFile
{
    private $className;

    private $namespace;

    private $file;

    public function __construct(string $className, string $namespace, string $file)
    {
        $this->className = $className;
        $this->namespace = $namespace;
        $this->file = $file;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getLocalName(): string
    {
        return $this->className;
    }

    public function getClassName(): string
    {
        return implode('\\', [ $this->namespace, $this->className ]);
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }
}
