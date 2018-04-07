<?php namespace Motorphp\SilexTools\ClassPattern;

interface MatcherConfigurator
{
    function addVisibilityMethod(PatternMethod $class): MatcherConfigurator;

    function addVisibilityConstant(PatternConstant $class) : MatcherConfigurator;

    function addAnnotationsClass(PatternClass $class) : MatcherConfigurator;

    function addAnnotationsMethod(PatternMethod $class) : MatcherConfigurator;

    function addAnnotationsConstant(PatternConstant $class) : MatcherConfigurator;

    function addAnnotationsClassMethod(PatternClass $pattern) : MatcherConfigurator;

    function addModifiersMethod(PatternMethod $class) : MatcherConfigurator;

    function addImplements(PatternClass $class);
}