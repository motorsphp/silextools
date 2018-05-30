<?php namespace Motorphp\SilexTools\Matcher;

use Motorphp\SilexTools\ClassPattern\Matches;

class MatchResults implements Matches
{
    /**
     * @var array | MatchEntry[]
     */
    private $matchEntries;

    /**
     * @param array|MatchEntry[] $matchEntries
     */
    public function __construct(array $matchEntries)
    {
        $this->matchEntries = $matchEntries;
    }

    /**
     * @param string $matchKey
     * @return array|\ReflectionParameter[]
     */
    public function getParams(string $matchKey): array
    {
        $mapper = function(MatchEntry $entry) {
            return $entry->param;
        };
        $entries = $this->mapEntries($matchKey, $mapper);
        return array_unique($entries);
    }

    /**
     * @param string $matchKey
     * @return array|\ReflectionClassConstant[]
     */
    public function getConstants(string $matchKey): array
    {
        $mapper = function(MatchEntry $entry) {
            return $entry->constant;
        };
        $entries = $this->mapEntries($matchKey, $mapper);
        return array_unique($entries);
    }

    /**
     * @param string $matchKey
     * @return array|\ReflectionMethod[]
     */
    public function getMethods(string $matchKey): array
    {
        $mapper = function(MatchEntry $entry) {
            return $entry->method;
        };
        $entries = $this->mapEntries($matchKey, $mapper);
        return array_unique($entries);
    }

    /**
     * @param string $matchKey
     * @return array|\ReflectionClass[]
     */
    public function getClasses(string $matchKey): ?array
    {
        $mapper = function(MatchEntry $entry) {
            return $entry->class;
        };
        $entries = $this->mapEntries($matchKey, $mapper);
        return array_unique($entries);
    }

    private function mapEntries(string $matchKey, \Closure $mapper) : array
    {
        $mapped = [];
        foreach ($this->matchEntries as $matchEntry) {
            if ($matchEntry->patternId->getMatchKey() === $matchKey) {
                $mappedEntry = $mapper($matchEntry);
                if (! empty($mappedEntry)) {
                    $mapped[] = $mappedEntry;
                }
            }
        }

        return empty($mapped) ? [] : $mapped;
    }

    private function getArrayOrNull(string $matchKey, array &$entries) : ?array
    {
        if (array_key_exists($matchKey, $entries)) {
            $matches = $entries[$matchKey];
            if (empty($matches) || !is_array($matches)) {
                return null;
            }
            return $matches;
        }
    }
}