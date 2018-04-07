<?php namespace Motorphp\SilexTools\Matcher;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\DocParser;
use Motorphp\SilexAnnotations\Reader\ConstantsReader;
use Motorphp\SilexTools\ClassPattern\PatternBuilder;

class FilterBuilder
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

    /**
     * @var string
     */
    private $matchType;

    public static function all(\Closure $configurator = null): FilterBuilder
    {
        $parser = new DocParser();
        $reader = new AnnotationReader($parser);
        $constantsReader = new ConstantsReader($parser, $reader);
        $builder = new FilterBuilder($constantsReader, 'all');

        if (!is_null($configurator)) {
            $builder->add($configurator);
        }
        return $builder;
    }

    public static function any(\Closure $configurator = null): FilterBuilder
    {
        $parser = new DocParser();
        $reader = new AnnotationReader($parser);
        $constantsReader = new ConstantsReader($parser, $reader);
        $builder = new FilterBuilder($constantsReader, 'any');

        if (!is_null($configurator)) {
            $builder->add($configurator);
        }
        return $builder;
    }

    /**
     * MatcherFactories constructor.
     * @param ConstantsReader $reader
     * @param string $matchType
     */
    public function __construct(ConstantsReader $reader, string $matchType)
    {
        $this->reader = $reader;
        $this->matchType = $matchType;
    }


    public function add(\Closure $configurator): FilterBuilder
    {
        $patternBuilder = new PatternBuilder($this->reader);
        /** @var PatternBuilder $patternBuilder */
        $patternBuilder = $configurator($patternBuilder);

        $builder = new MatcherBuilder($this->reader);
        $patternBuilder->build()->configureMatcher($builder);

        $this->matchers[] = $builder->build();
        return $this;
    }

    public function build() : FilterMatcher
    {
        return new FilterMatcher($this->matchers, $this->matchType);
    }
}