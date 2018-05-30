<?php namespace Motorphp\SilexTools\Bootstrap;

class Signatures
{
    /**
     * @param string $class
     * @return \ReflectionMethod
     * @throws \ReflectionException
     */
    public function configureProviders(string $class): \ReflectionMethod
    {
        return new \ReflectionMethod($class, __FUNCTION__);
    }

    /**
     * @param string $class
     * @return \ReflectionMethod
     * @throws \ReflectionException
     */
    public function configureHttp(string $class) : \ReflectionMethod
    {
        return new \ReflectionMethod($class, __FUNCTION__);
    }

    /**
     * @param string $class
     * @return \ReflectionMethod
     * @throws \ReflectionException
     */
    public function configureFactories(string $class): \ReflectionMethod
    {
        return new \ReflectionMethod($class, __FUNCTION__);
    }
}