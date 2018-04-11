<?php namespace Motorphp\SilexTools\Matcher;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\DocParser;
use Motorphp\SilexAnnotations\Reader\ConstantsReader;
use Motorphp\SilexTools\ClassPattern\PatternBuilder;

class SelectorBuilder
{
    /**
    private $matchType;

    /**
     * @var array| Matcher
     */
    private $matchers = [];

    /**
     * @var ConstantsReader
     */
    private $reader;

    public static function selector(\Closure $configurator): SelectorMatcher
    {
        return SelectorBuilder::instance()->add($configurator)->build();
    }

    public static function instance(ConstantsReader $reader = null): SelectorBuilder
    {
        $constantsReader = null;
        if (is_null($reader)) {
            $parser = new DocParser();
            $reader = new AnnotationReader($parser);
            $constantsReader = new ConstantsReader($parser, $reader);
        } else {
            $constantsReader = $reader;
        }

        return new SelectorBuilder($constantsReader);
    }

    /**
     * MatcherFactories constructor.
     * @param ConstantsReader $reader
     */
    public function __construct(ConstantsReader $reader)
    {
        $this->reader = $reader;
    }


    public function add(\Closure $configurator): SelectorBuilder
    {
        $patternBuilder = new PatternBuilder($this->reader);
        /** @var PatternBuilder $patternBuilder */
        $patternBuilder = $configurator($patternBuilder);

        $builder = new MatcherBuilder($this->reader);
        $patternBuilder->build()->configureMatcher($builder);

        $this->matchers[] = $builder->build();
        return $this;
    }

    public function addAndBuild(\Closure $configurator): SelectorMatcher
    {
        $this->add($configurator);
        return $this->build();
    }

    public function build() : SelectorMatcher
    {
        return new SelectorMatcher($this->matchers);
    }
}