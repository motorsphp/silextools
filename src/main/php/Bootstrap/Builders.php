<?php namespace Motorphp\SilexTools\Bootstrap;

use Motorphp\SilexAnnotations\Reader\ConstantsReader;

class Builders
{
    /**
     * @param string $class
     * @param ConstantsReader $reader
     * @return BuildConfigureProviders
     * @throws \ReflectionException
     */
    public static function configureProvidersWithReader(string $class, ConstantsReader $reader): BuildConfigureProviders
    {
        return Builders::configureProviders($class, new BuildContext($reader));
    }

    /**
     * @param string $class
     * @return BuildConfigureProviders
     * @throws \ReflectionException
     */
    public static function configureProviders(string $class, BuildContext $context): BuildConfigureProviders
    {
        $reflection = new \ReflectionMethod($class, __FUNCTION__);
        return new BuildConfigureProviders($reflection, $context);
    }

    /**
     * @param string $class
     * @param BuildContext $context
     * @return BuildConfigureHttp
     * @throws \ReflectionException
     */
    public static function configureHttp(string $class, BuildContext $context) : BuildConfigureHttp
    {
        $reflection = new \ReflectionMethod($class, __FUNCTION__);
        $containerArg = 'container';
        $controllerFactoryArg = 'controllers';

        return new BuildConfigureHttp($containerArg, $controllerFactoryArg, $reflection, $context);
    }

    /**
     * @param string $class
     * @param ConstantsReader $reader
     * @return BuildConfigureHttp
     * @throws \ReflectionException
     */
    public static function configureHttpWithReader(string $class, ConstantsReader $reader): BuildConfigureHttp
    {
        $context = new BuildContext($reader);
        return Builders::configureHttp($class, $context);
    }

    /**
     * @param string $class
     * @param ConstantsReader $reader
     * @return BuildConfigureFactories
     * @throws \ReflectionException
     */
    public static function configureFactories(string $class, ConstantsReader $reader): BuildConfigureFactories
    {
        $reflection = new \ReflectionMethod($class, __FUNCTION__);
        return new BuildConfigureFactories($reflection, new BuildContext($reader));
    }
}