<?php namespace Motorphp\SilexTools\ClassPattern;

abstract class PatterBuilderAbstract
{
    public function expression(): PatternBuilder
    {
        return $this->and();
    }

    abstract public function and(): PatternBuilder;

    public function andMethod($patternKey): PatternBuilderMethod
    {
        return $this->and()->methodPattern($patternKey);
    }

    public function andConstant($patternKey): PatternBuilderConstant
    {
        return $this->and()->constantPattern($patternKey);
    }

    public function andClass($patternKey): PatternBuilderClass
    {
        return $this->and()->classPattern($patternKey);
    }
}