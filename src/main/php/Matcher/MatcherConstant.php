<?php namespace Motorphp\SilexTools\Matcher;

use Motorphp\SilexTools\ClassPattern\PatternId;

interface MatcherConstant
{
    public function getPatternId(): PatternId;

    public function matchConstant(\ReflectionClassConstant $reflector, MatchResultsCollector $matches): bool;
}