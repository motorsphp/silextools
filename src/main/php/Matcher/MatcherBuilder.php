<?php namespace Motorphp\SilexTools\Matcher;

use Motorphp\SilexAnnotations\Reader\ConstantsReader;
use Motorphp\SilexTools\ClassPattern\PatternClass;
use Motorphp\SilexTools\ClassPattern\PatternConstant;
use Motorphp\SilexTools\ClassPattern\PatternId;
use Motorphp\SilexTools\ClassPattern\PatternMethod;
use Motorphp\SilexTools\ClassPattern\MatcherConfigurator;

class MatcherBuilder implements MatcherConfigurator
{
    /**
     * array | MatcherClass[]
     */
    private $classMatchers = [];

    /**
     * @var array | MatcherMethod
     */
    private $methodMatchers = [];

    /**
     * @var array| MatcherConstant
     */
    private $constantMatchers = [];

    /**
     * @var array| PatternId
     */
    private $patterns = [];

    /**
     * @var ConstantsReader
     */
    private $reader;

    public function __construct(ConstantsReader $reader)
    {
        $this->reader = $reader;
    }

    function addImplements(PatternClass $pattern) : MatcherConfigurator
    {
        $patternId = $pattern->getId();
        $interfaces = $pattern->getInterfaces();
        $this->classMatchers[]= new MatcherImplements($patternId, $interfaces);
        $this->patterns[$patternId->toString()] = $patternId;

        return $this;
    }

    function addAnnotationsClass(PatternClass $pattern): MatcherConfigurator
    {
        $patternId = $pattern->getId();
        $annotations = $pattern->getClassAnnotations();
        $this->classMatchers[]= new MatcherAnnotations(
            $patternId,
            $annotations,
            $matchType = 'any',
            $this->reader
        );
        $this->patterns[$patternId->toString()] = $patternId;

        return $this;
    }

    function addAnnotationsClassMethod(PatternClass $pattern) : MatcherConfigurator
    {
        $patternId = $pattern->getId();
        $annotations = $pattern->getClassAnnotations();
        $this->methodMatchers[] = new MatcherAnnotations(
            $patternId,
            $annotations,
            $matchType = 'any',
            $this->reader
        );
        $this->patterns[$patternId->toString()] = $patternId;

        return $this;
    }

    function addAnnotationsMethod(PatternMethod $pattern) : MatcherConfigurator
    {
        $patternId = $pattern->getId();
        $annotations = $pattern->getAnnotations();
        $this->methodMatchers[] = new MatcherAnnotations(
            $patternId,
            $annotations,
            $matchType = $pattern->getAnnotationsMatchPolicy(),
            $this->reader
        );
        $this->patterns[$patternId->toString()] = $patternId;

        return $this;
    }

    function addAnnotationsConstant(PatternConstant $pattern) : MatcherConfigurator
    {
        $patternId = $pattern->getId();
        $annotations = $pattern->getAnnotations();
        $this->constantMatchers[] = new MatcherAnnotations(
            $patternId,
            $annotations,
            $matchType = $pattern->getAnnotationsMatchPolicy(),
            $this->reader
        );
        $this->patterns[$patternId->toString()] = $patternId;

        return $this;
    }


    function addVisibilityConstant(PatternConstant $pattern): MatcherConfigurator
    {
        $visibility = $pattern->getVisibility();
        $patternId = $pattern->getId();
        $this->constantMatchers[] = new MatcherVisibility($patternId, $visibility);
        $this->patterns[$patternId->toString()] = $patternId;

        return $this;
    }


    function addVisibilityMethod(PatternMethod $pattern): MatcherConfigurator
    {
        $visibility = $pattern->getVisibility();
        $patternId = $pattern->getId();
        $this->methodMatchers[] = new MatcherVisibility($patternId, $visibility);
        $this->patterns[$patternId->toString()] = $patternId;

        return $this;
    }

    function addModifiersMethod(PatternMethod $pattern): MatcherConfigurator
    {
        $visibility = $pattern->getModifiers();
        $patternId = $pattern->getId();
        $this->methodMatchers[] = new MatcherModifiers($patternId, $visibility);
        $this->patterns[$patternId->toString()] = $patternId;

        return $this;
    }

    function build(): Matcher
    {
        return new Matcher($this->classMatchers, $this->methodMatchers, $this->constantMatchers, $this->patterns);
    }

}