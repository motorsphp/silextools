<?php namespace Motorphp\SilexTools\Bootstrap;

use Motorphp\SilexTools\NetteLibrary\BootstrapWritter;
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
        $signatures = new Signatures();
        $configurator = function (BootstrapWritter $writer) use($class, $signatures) {
            $writer->withMethod(new BodyWriterRoute(), $signatures->configureHttp($class));
            $writer->withMethod(new BodyWriterProvider(), $signatures->configureProviders($class));

            $signature = $signatures->configureFactories($class);
            $methodWriter = BodyWriterFactory::fromSignature($signature);
            $writer->withMethod($methodWriter, $signature);
        };

        $builder = new BootstrapBuilder($configurator);
        $builder->withNamespaceAndClassname($class);
        return $builder;
    }
}
