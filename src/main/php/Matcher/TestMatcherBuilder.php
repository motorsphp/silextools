<?php namespace Motorphp\SilexTools\Matcher;

use Motorphp\SilexTools\ClassPattern\Matcher;
use Motorphp\SilexTools\ClassPattern\MatcherBuilder;

class TestMatcherBuilder implements MatcherBuilder
{
    /** @var Test */
    private $test;

    /**
     * @var array| string[]
     */
    private $matchKeys = [];

    /**
     * @var TypeAcceptor
     */
    private $typeAcceptor;


    function setTest(Test $test) : TestMatcherBuilder
    {
        $this->test = $test;
        return $this;
    }

    function setAppliesTo(string $reflectorType): MatcherBuilder
    {
        $this->typeAcceptor = new TypeAcceptor($reflectorType);
        return $this;
    }

    function addMatchLabel($key): MatcherBuilder
    {
        $this->matchKeys[] = $key;
        return $this;
    }

    function build(): Matcher
    {
        return new TestMatcherAdapter(
            $this->test,
            new MatchFactory($this->typeAcceptor, $this->matchKeys),
            $this->typeAcceptor
        );
    }
}