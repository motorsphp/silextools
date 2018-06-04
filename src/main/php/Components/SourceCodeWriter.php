<?php namespace Motorphp\SilexTools\Components;

interface SourceCodeWriter
{
    public function writeString(string $value) : Value;

    public function writeClassType(\ReflectionClass $value) : Value;

    public function writeClassName(\ReflectionClass $value) : Value;

    public function writeConstant(\ReflectionClassConstant $value) : Value;

    public function writeStaticInvocation(\ReflectionMethod $value) : Value;

}