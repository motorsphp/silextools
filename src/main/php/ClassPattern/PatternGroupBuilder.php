<?php namespace Motorphp\SilexTools\ClassPattern;

use Doctrine\Common\Annotations\Reader;

class PatternGroupBuilder
{
    /**
     * @var array
     */
    private $matchKeys = [];

    /**
     * @var array|Pattern[]
     */
    private $patterns = [];

    /**
     * @var Reader
     */
    private $reader;

    /**
     * MatcherFactories constructor.
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function addMatchKey(string $key)
    {
        $this->matchKeys[] = $key;
        return $this;
    }

    public function addPattern(Pattern $pattern) : PatternGroupBuilder
    {
        $this->patterns[] = $pattern;
        return $this;
    }

    public function build() : PatternGroup
    {
        $id = PatternId::next();
        return new PatternGroup($id,$this->patterns, $this->matchKeys);
    }

    public function constantPattern(): PatternConstant\Builder
    {
        return new PatternConstant\Builder($this->reader);
    }

    public function methodPattern(): PatternMethod\Builder
    {
        return new PatternMethod\Builder($this->reader);
    }

    public function classPattern(): PatternClass\Builder
    {
        return new PatternClass\Builder($this->reader);
    }
}