<?php namespace Motorphp\SilexTools\Bootstrap;

use Motorphp\SilexTools\Components\Components;

interface BootstrapBuilder
{
    /**
     * @param string $class
     * @return BootstrapBuilder
     * @throws \ReflectionException
     */
    public function withSameNamespaceAsClass(string $class) : BootstrapBuilder;

    public function withNamespace(string $namespace) : BootstrapBuilder;

    /**
     * @param string $class
     * @return BootstrapBuilder
     * @throws \ReflectionException
     */
    public function withClassname(string $class) : BootstrapBuilder;

    public function withComponents(Components $components) : BootstrapBuilder;

    /**
     * @param string|string[]$folders
     * @return BootstrapBuilder
     */
    public function withComponentsFrom($folders) : BootstrapBuilder;

    function build(): string;
}