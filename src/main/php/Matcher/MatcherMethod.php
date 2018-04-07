<?php namespace Motorphp\SilexTools\Matcher;

use Motorphp\SilexTools\ClassPattern\PatternId;

interface MatcherMethod
{
    public function getPatternId(): PatternId;

    public function matchMethod(\ReflectionMethod $reflector, MatchResultsCollector $matches): bool;
}