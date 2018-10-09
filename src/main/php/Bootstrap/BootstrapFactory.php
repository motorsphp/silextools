<?php namespace Motorphp\SilexTools\Bootstrap;

use Motorphp\SilexTools\NetteLibrary\BootstrapBuilderAdapter;
use Motorphp\SilexTools\NetteLibrary\Methods\BodyWriterFactory;
use Motorphp\SilexTools\NetteLibrary\Methods\BodyWriterProvider;
use Motorphp\SilexTools\NetteLibrary\Methods\BodyWriterRoute;

class BootstrapFactory
{
    /**
     * @param string $class
     * @return BootstrapBuilder
     * @throws \ReflectionException
     */
    public static function bootstrap(string $class) : BootstrapBuilder
    {
        $builder = new BootstrapBuilderAdapter();
        $builder->withSameNamespaceAsClass($class)->withClassname($class);

        $signatures = new Signatures();

        $builder->withMethod(new BodyWriterRoute(), $signatures->configureHttp($class));
        $builder->withMethod(new BodyWriterProvider(), $signatures->configureProviders($class));

        $signature = $signatures->configureFactories($class);
        $writer = BodyWriterFactory::fromSignature($signature);
        $builder->withMethod($writer, $signature);

        return $builder;
    }
}