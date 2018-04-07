<?php namespace Motorphp\SilexTools\Matcher;

use Motorphp\SilexTools\ClassPattern\PatternId;

class Matcher
{
    /**
     * @var array| MatcherClass[]
     */
    private $class = [];

    /**
     * @var array| MatcherMethod[]
     */
    private $method = [];

    /**
     * @var array| MatcherConstant[]
     */
    private $constant = [];

    /**
     * @var PatternId[]
     */
    private $patterns;

    public function __construct(
        array $class,
        array $method,
        array $constant,
        array $patterns
    ) {
        $this->class = $class;
        $this->method = $method;
        $this->constant = $constant;
        $this->patterns = $patterns;
    }

    public function hasMatchers() : bool
    {
        $isEmpty = empty($this->class) && empty($this->method) && empty($this->constant);
        return !$isEmpty;
    }

    /**
     * @return array|PatternId[]
     */
    public function getPatterns()
    {
        return $this->patterns;
    }

    /**
     * @param \ReflectionClass $class
     * @param MatchResultsCollector $collector
     * @return array[]|PatternId[]
     */
    public function matchClassMethods(\ReflectionClass $class, MatchResultsCollector $collector): array
    {
        $methods = $class->getMethods();
        return $this->matchMethods($methods, $collector);
    }

    /**
     * @param \ReflectionClass $class
     * @param MatchResultsCollector $collector
     * @return array[]|PatternId[]
     */
    public function matchClassConstants(\ReflectionClass $class, MatchResultsCollector $collector): array
    {
        $constants = $class->getReflectionConstants();
        return $this->matchConstants($constants, $collector);
    }

    /**
     * @param array|\ReflectionMethod[] $reflections
     * @param MatchResultsCollector $collector
     * @return array[]|PatternId[]
     */
    public function matchMethods(array $reflections, MatchResultsCollector $collector): array
    {
        $nonMatching = [];

        foreach ($this->method as $matcher) {
            $matches = false;
            foreach ($reflections as $method) {
                if ($matcher->matchMethod($method, $collector)) {
                    $matches = true;
                }
            }

            if (! $matches) {
                $nonMatching[] = $matcher->getPatternId();
            }
        }

        return $nonMatching;
    }

    /**
     * @param array|\ReflectionClass[] $reflections
     * @param MatchResultsCollector $collector
     * @return array[]|PatternId[]
     */
    public function matchClasses(array $reflections, MatchResultsCollector $collector) : array
    {
        $nonMatching = [];

        foreach ($this->class as $matcher) {
            $matches = false;
            foreach ($reflections as $class) {
                if ($matcher->matchClass($class, $collector)) {
                    $matches = true;
                }
            }

            if (! $matches) {
                $nonMatching[] = $matcher->getPatternId();
            }
        }

        return $nonMatching;
    }

    /**
     * @param array|\ReflectionClassConstant[] $reflections
     * @param MatchResultsCollector $collector
     * @return array[]|PatternId[]
     */
    public function matchConstants(array $reflections, MatchResultsCollector $collector): array
    {
        $nonMatching = [];

        foreach ($this->constant as $matcher) {
            $matches = false;
            foreach ($reflections as $constant) {
                if ($matcher->matchConstant($constant, $collector)) {
                    $matches = true;
                }
            }

            if (! $matches) {
                $nonMatching[] = $matcher->getPatternId();
            }
        }

        return $nonMatching;
    }
}