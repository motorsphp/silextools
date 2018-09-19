<?php namespace Motorphp\SilexTools\Matcher\MatchVisibility;

class Reader
{
    public function isPrivate(\Reflector $reflector) : bool
    {
        if ($reflector instanceof \ReflectionMethod) {
            return $reflector->isPrivate();
        }

        if ($reflector instanceof \ReflectionClassConstant) {
            return $reflector->isPrivate();
        }

        return false;
    }

    public function isProtected(\Reflector $reflector) : bool
    {
        if ($reflector instanceof \ReflectionMethod) {
            return $reflector->isProtected();
        }

        if ($reflector instanceof \ReflectionClassConstant) {
            return $reflector->isProtected();
        }

        return false;
    }

    public function isPublic(\Reflector $reflector) : bool
    {
        if ($reflector instanceof \ReflectionMethod) {
            return $reflector->isPublic();
        }

        if ($reflector instanceof \ReflectionClassConstant) {
            return $reflector->isPublic();
        }

        return false;
    }
}