<?php namespace Motorphp\SilexTools\NetteLibrary;

use Nette\PhpGenerator\PhpLiteral;

class PhpLiterals
{
    public static function classLiteral(string $class): PhpLiteral
    {
        return new PhpLiteral($class . '::class');
    }
}