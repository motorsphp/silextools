<?php namespace Motorphp\SilexTools\NetteLibrary;

use Nette\PhpGenerator\PhpLiteral;

class PhpLiterals
{
    private static $tokens = [
        T_DOUBLE_COLON => '::'
    ];

    public static function class(\ReflectionClass $reflection): PhpLiteral
    {
        $literal = $reflection->getShortName();
        return new PhpLiteral($literal);
    }

    public static function className(\ReflectionClass $reflection): PhpLiteral
    {
        $literal = $reflection->getShortName() . self::$tokens[T_DOUBLE_COLON] . 'class';
        return new PhpLiteral($literal);
    }

    public static function constant(\ReflectionClassConstant $reflection): PhpLiteral
    {
        $declaringClass = $reflection->getDeclaringClass();

        $literal = $declaringClass->getShortName()
            . self::$tokens[T_DOUBLE_COLON]
            . $reflection->getName()
        ;
        return new PhpLiteral($literal);
    }

    public static function staticMethod(\ReflectionMethod $reflection): PhpLiteral
    {
        $declaringClass = $reflection->getDeclaringClass();

        $literal = $declaringClass->getShortName()
            . self::$tokens[T_DOUBLE_COLON]
            . $reflection->getName()
        ;
        return new PhpLiteral($literal);
    }
}