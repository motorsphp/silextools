<?php namespace Motorphp\SilexTools\ClassPattern;

abstract class PatternBuilder
{
    public function done(PatternGroupBuilder $builder): PatternGroupBuilder
    {
        return $this->and($builder);
    }

    public function and(PatternGroupBuilder $builder) : PatternGroupBuilder
    {
        $pattern = $this->build();
        return $builder->addPattern($pattern);
    }

    abstract public function build(): Pattern;
}