<?php namespace Motorphp\SilexTools\Matcher;

use Motorphp\SilexTools\ClassPattern\Matches;
use Motorphp\SilexTools\ClassPattern\PatternId;

class SelectorMatcher
{
    /**
     * @var Matcher
     */
    private $matchers = [];

    public function __construct(array $matchers) {
        $this->matchers = $matchers;
    }

    public function select(array $files) : Matches
    {
        $matchers = array_filter($this->matchers, function (Matcher $matcher) {
            return $matcher->hasMatchers();
        });

        $collector = new MatchResultsCollector();

        if (empty($matchers)) {
            $collector->list();
        }

        foreach ($files as $file) {
            try {
                $class = new \ReflectionClass($file);
                foreach ($matchers as $matcher) {
                    $matchesCount = $collector->count();
                    $nonMatching = $this->matchPattern($class, $matcher, $collector);
                    if (! empty($nonMatching)) {
                        $collector->remove($nonMatching, $matchesCount);
                    }

                }
            } catch (\ReflectionException $e) { /** empty */ }
        }

        return $collector->list();
    }

    /**
     * @param \ReflectionClass $class
     * @param Matcher $matcher
     * @param MatchResultsCollector $collector
     * @return array|PatternId[]
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
