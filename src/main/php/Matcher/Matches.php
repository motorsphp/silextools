<?php namespace Motorphp\SilexTools\Matcher;

use Motorphp\SilexTools\ClassPattern;

use Motorphp\SilexTools\ClassScanner\ClassFile;
use Motorphp\SilexTools\ClassScanner\Scanner;

class Matches
{
    public static function scanAndSelect(array $source, Scanner $scanner, SelectorMatcher $selector): ClassPattern\Matches
    {
        $classFiles = $scanner->scanAll($source);
        $classes = array_map(function (ClassFile $class) {
            return $class->getClassName();
        }, $classFiles);

        return $selector->select($classes);
    }

    public static function defaultScanAndSelect(array $sources, SelectorMatcher $selector): ClassPattern\Matches
    {
        return Matches::scanAndSelect($sources, new Scanner(), $selector);
    }
}
