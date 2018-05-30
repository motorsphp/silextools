<?php namespace Motorphp\SilexTools\Components\ServiceCallback;

class Bindings
{
    public static function classname(\ReflectionMethod $reflector) : Binding
    {
        $clazz = $reflector->getDeclaringClass();
        return new BindingClassnameKey($clazz, $reflector);
    }

    public static function returnType(\ReflectionMethod $reflector) : Binding
    {
        $returnType = $reflector->getReturnType();
        /** @var Binding $strategy */
        $binding = null;

        if (!is_null($returnType)) {
            if (!$returnType->isBuiltin()) {
                $returnTypeClass = $returnType->getName();
                try {
                    $reflectionClass = new \ReflectionClass($returnTypeClass);
                    $binding = new BindingClassnameKey($reflectionClass, $reflector);
                } catch (\ReflectionException $e) {
                    $binding = new BindingScalarKey($returnTypeClass, $reflector);
                }
            }
        }

        if (empty($binding)) {
            throw new \RuntimeException('failed to create key');
        }

        return $binding;
    }
}