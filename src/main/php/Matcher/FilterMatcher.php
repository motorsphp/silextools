<?php namespace Motorphp\SilexTools\Matcher;

use Motorphp\SilexTools\ClassPattern\Filter;
use Motorphp\SilexTools\ClassPattern\Matches;
use Motorphp\SilexTools\ClassPattern\PatternId;

class FilterMatcher implements Filter
{
    /**
     * @var Matcher[]
     */
    private $matchers;

    /**
     * @var string
     */
    private $matchType;

    public function __construct(array $matchers, string $matchType) {
        $this->matchers = $matchers;
        $this->matchType = $matchType;
    }

    public function isMatching(string $class):bool
    {
        $actualMatches = $this->match($class);
        return !is_null($actualMatches);
    }

    /**
     * @param string $class
     * @return MatchResults|null
     */
    public function match(string $class) : ?Matches
    {
        $matches = new MatchResultsCollector();
        return $this->matchWithBuilder($class, $matches);
    }

    public function matchWithBuilder(string $class, MatchResultsCollector $collector): ?MatchResults
    {
        try {
            $matches = [];
            $class = new \ReflectionClass($class);
            foreach ($this->matchers as $matcher) {
                $matchesCount = $collector->count();
                $nonMatching = $this->matchPattern($class, $matcher, $collector);
                $matches[] = empty($nonMatching);

                if (! empty($nonMatching)) {
                    $collector->remove($nonMatching, $matchesCount);
                }
            }

            $matchFound = $this->isMatchFound($matches);
            return $matchFound ? $collector->list() : null;
        } catch (\ReflectionException $e) {
            return null;
        }
    }

    private function isMatchFound(array $matches)
    {
        if ($this->matchType === 'all') {
            return array_reduce($matches, function (bool $carry, bool $match) {
                return $carry && $match;
            }, true);
        }

        return array_reduce($matches, function (bool $carry, bool $match) {
            return $carry || $match;
        }, false);
    }

    /**
     * @param \ReflectionClass $class
     * @param Matcher $matcher
     * @param MatchResultsCollector $collector
     * @return array
     */
    private function matchPattern(\ReflectionClass $class, Matcher $matcher, MatchResultsCollector $collector): array
    {
        $nonMatching = [];
        $nonMatching = array_merge($nonMatching, $matcher->matchClasses([$class], $collector));
        $nonMatching = array_merge($nonMatching, $matcher->matchClassMethods($class, $collector));
        $nonMatching = array_merge($nonMatching, $matcher->matchClassConstants($class, $collector));

        $reducer = function (array $carry, PatternId $item) {
            $carry[$item->toString()] = $item;
            return $carry;
        };
        $nonMatching = array_values(array_reduce($nonMatching, $reducer, []));

        return $nonMatching;
    }
}
