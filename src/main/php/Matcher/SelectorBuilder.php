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
        return SelectorBuilder::instance($configurator)->build();
    }

    public static function instance(\Closure $configurator = null): SelectorBuilder
    {
        $parser = new DocParser();
        $reader = new AnnotationReader($parser);
        $constantsReader = new ConstantsReader($parser, $reader);
        $builder = new SelectorBuilder($constantsReader);

        if (!is_null($configurator)) {
            $builder->add($configurator);
        }
        return $builder;
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

    public function build() : SelectorMatcher
    {
        return new SelectorMatcher($this->matchers);
    }
}