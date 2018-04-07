<?php namespace Motorphp\SilexTools\Matcher;

use Motorphp\SilexTools\ClassPattern\PatternId;

interface MatcherClass
{
    public function getPatternId(): PatternId;

    public function matchClass(\ReflectionClass $reflector, MatchResultsCollector $matches): bool;
}