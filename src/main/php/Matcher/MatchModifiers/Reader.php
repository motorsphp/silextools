<?php namespace Motorphp\SilexTools\Matcher\MatchModifiers;

class Reader
{
    public function isAbstract(\Reflector $reflector)
    {
        if ($reflector instanceof \ReflectionMethod) {
            return $reflector->isAbstract();
        }

        if ($reflector instanceof \ReflectionClass) {
            return $reflector->isAbstract();
        }

        return false;
    }

    public function isFinal(\Reflector $reflector)
    {
        if ($reflector instanceof \ReflectionMethod) {
            return $reflector->isFinal();
        }

        if ($reflector instanceof \ReflectionClass) {
            return $reflector->isFinal();
        }

        return false;
    }

    public function isStatic(\Reflector $reflector)
    {
        if ($reflector instanceof \ReflectionMethod) {
            return $reflector->isStatic();
        }

        return false;
    }
}