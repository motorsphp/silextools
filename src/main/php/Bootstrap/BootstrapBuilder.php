<?php namespace Motorphp\SilexTools\Bootstrap;

use Motorphp\SilexTools\Bootstrap\Factories;
use Motorphp\SilexTools\Bootstrap\Providers;
use Motorphp\SilexTools\Bootstrap\Routes;

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

    public function withRoutes(\ReflectionMethod $signature) : MethodBuilder;

    public function withProviders(\ReflectionMethod $signature) : MethodBuilder;

    public function withFactories(\ReflectionMethod $signature) : MethodBuilder;

    function build(): string;
}