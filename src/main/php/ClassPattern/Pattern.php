<?php namespace Motorphp\SilexTools\ClassPattern;

interface Pattern
{
    function getId(): PatternId;

    function configureMatcher(MatcherConfigurator $configurator);
}