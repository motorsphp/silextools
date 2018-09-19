<?php namespace Motorphp\SilexTools\ClassPattern;

interface MatcherFactories
{
    function visibility(int $visibility) : MatcherBuilder;

    function modifiers(int $modifiers) : MatcherBuilder;

    function annotations(array $annotations, $matchType) : MatcherBuilder;

    function implements(array $interfaces) : MatcherBuilder;

    function anyClass() : MatcherBuilder;
}