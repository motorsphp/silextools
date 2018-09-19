<?php namespace Motorphp\SilexTools\ClassPattern;

interface Matcher
{
    public function appliesTo(string $reflectorType): bool;

    public function match(\Reflector $reflector) : ?Match;
}