<?php namespace Motorphp\SilexTools\Components;

interface SourceCodeWriter
{
    public function writeString(string $value);

    public function writeClassType(\ReflectionClass $value);

    public function writeClassName(\ReflectionClass $value);

    public function writeConstant(\ReflectionClassConstant $value);

    public function writeStaticInvocation(\ReflectionMethod $value);

}