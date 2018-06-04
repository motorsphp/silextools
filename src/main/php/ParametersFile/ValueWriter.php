<?php namespace Motorphp\SilexTools\ParametersFile;

use Motorphp\SilexTools\Components\SourceCodeWriter;
use Motorphp\SilexTools\Components\Value;

class ValueWriter implements SourceCodeWriter
{
    public function writeString(string $value) : Value
    {
        return new Value($value);
    }

    public function writeClassType(\ReflectionClass $value) : Value
    {
        $literal = $value->getNamespaceName();
        return new Value($literal);
    }

    public function writeClassName(\ReflectionClass $value) : Value
    {
        $literal = $value->getNamespaceName() . '::class';
        return new Value($literal);
    }

    public function writeConstant(\ReflectionClassConstant $value) : Value
    {
        $literal = $value->getDeclaringClass()->getNamespaceName()
            . '::'
            . $value->getName();
        return new Value($literal);
    }

    public function writeStaticInvocation(\ReflectionMethod $value) : Value
    {
        $literal = $value->getDeclaringClass()->getNamespaceName()
            . '::'
            . $value->getName();

        return new Value($literal);
    }
}