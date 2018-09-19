<?php namespace Motorphp\SilexTools\Matcher;

use Motorphp\SilexTools\ClassPattern;

use Motorphp\SilexTools\ClassPattern\Match;
use Motorphp\SilexTools\ClassPattern\Matcher;
use Motorphp\SilexTools\ClassPattern\MatchResults;
use Motorphp\SilexTools\ClassPattern\PatternId;
use Motorphp\SilexTools\ClassScanner\ClassFile;
use Motorphp\SilexTools\ClassScanner\Scanner;

class Matches
{
    public static function select(array $classes, array $matchers)
    {
        $matches = [];
        $notMatching = [];
        $matchReader = new Matches();
        $groups = $matchReader->groupMatchesByType($matchers);
        foreach ($classes as $class) {

            /** @var Matcher[] $matchers */
            $matchers = $groups[\ReflectionClass::class];
            if (count($matchers)) {
                $matchReader->matchReflectors([$class], $matchers, $matches, $notMatching);
            }

            $matchers = $groups[\ReflectionMethod::class];
            if (count($matchers)) {
                $matchReader->matchReflectors($class->getMethods(), $matchers, $matches, $notMatching);
            }

            $matchers = $groups[\ReflectionClassConstant::class];
            if (count($matchers)) {
                $matchReader->matchReflectors($class->getConstants(), $matchers, $matches, $notMatching);
            }
        }

        $pruned = $matchReader->prune($notMatching, $matches);
        return new MatchResults($pruned);
    }

    /**
     * @param array|Matcher[] $matchers
     * @return array
     */
    private function groupMatchesByType(array $matchers)
    {
        $reducer = function (array $groups, Matcher $matcher) {
            if ($matcher->appliesTo(\ReflectionClass::class)) {
                $groups[\ReflectionClass::class][] = $matcher;
            } else if ($matcher->appliesTo(\ReflectionMethod::class)) {
                $groups[\ReflectionMethod::class][] = $matcher;
            } else if ($matcher->appliesTo(\ReflectionClassConstant::class)) {
                $groups[\ReflectionClassConstant::class][] = $matcher;
            }

            return $groups;
        };

        return array_reduce($matchers, $reducer, [
            \ReflectionClass::class => [],
            \ReflectionMethod::class => [],
            \ReflectionClassConstant::class => [],
        ]);
    }

    /**
     * @param array|\Reflector $reflectors
     * @param array|Matcher[] $matchers
     * @param array $matches
     * @param array $notMatching
     */
    private function matchReflectors(array $reflectors, array $matchers, array &$matches, array &$notMatching)
    {
        foreach ($reflectors as $reflector) {
            foreach ($matchers as $matcher) {
                $match = $matcher->match($reflector);
                if ($match instanceof Match) {
                    $matches[] = $match;
                } else {
                    foreach ($matcher->getBelongsTo() as $patternId) {
                        $notMatching[] = $patternId;
                    }
                }
            }
        }
    }

    /**
     * @param array|PatternId[] $notMatching
     * @param array|Match[] $matches
     * @return array|Match[]
     */
    private function prune(array $notMatching, array $matches): array
    {
        $filter = function (Match $entry) use ($notMatching) {
            return !PatternId::inArray($entry->patternId, $notMatching);
        };

        return array_filter($matches, $filter);
    }
}
