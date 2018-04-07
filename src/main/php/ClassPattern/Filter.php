<?php namespace Motorphp\SilexTools\ClassPattern;

interface Filter
{
    public function isMatching(string $class): bool;

    /**
     * @param string $class
     * @return Matches|null
     */
    public function match(string $class) : ?Matches;
}