<?php namespace Motorphp\SilexTools\ClassPattern;

class MatchResults
{
    /**
     * @var array | Match[]
     */
    private $matchEntries;

    /**
     * @param array|Match[] $matchEntries
     */
    public function __construct(array $matchEntries)
    {
        $this->matchEntries = $matchEntries;
    }

    public function filterByKey(string $matchKey): MatchResults
    {
        $entries =  array_filter($this->matchEntries, function (Match $entry) use ($matchKey) {
            return in_array($matchKey, $entry->matchKeys);
        });

        return new MatchResults($entries);
    }

    /**
     * @return array|\ReflectionClassConstant[]
     */
    public function constants(): array
    {
        $filter = function (Match $entry) {
            return $entry->reflector instanceof \ReflectionClassConstant;
        };
        return $this->getReflectors($filter);
    }

    /**
     * @return array|\ReflectionMethod[]
     */
    public function methods(): array
    {
        $filter = function (Match $entry) {
            return $entry->reflector instanceof \ReflectionMethod;
        };
        return $this->getReflectors($filter);
    }

    /**
     * @return array|\ReflectionClass[]
     */
    public function classes(): array
    {
        $filter = function (Match $entry) {
            return $entry->reflector instanceof \ReflectionClass;
        };
        return $this->getReflectors($filter);
    }

    /**
     * @return array|\Reflector[]
     */
    public function all(): array
    {
        return array_map(
            function (Match $entry) {
                return $entry->reflector;
            }, $this->matchEntries
        );
    }

    private function getReflectors(\Closure $filter)
    {
        return array_map(
            function (Match $entry) {
                return $entry->reflector;
            },
            array_filter($this->matchEntries, $filter)
        );
    }
}