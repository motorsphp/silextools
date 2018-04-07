<?php namespace Motorphp\SilexTools\Matcher;

use Motorphp\SilexTools\ClassPattern\PatternId;

class MatchResultsCollector
{
    /**
     * @var array|MatchEntry[]
     */
    private $class = [];

    /**
     * @var array|\ReflectionMethod[]
     */
    private $method = [];

    /**
     * @var array|\ReflectionClassConstant[]
     */
    private $constant = [];

    /**
     * @var array|\ReflectionParameter[]
     */
    private $param = [];

    /**
     * @var array|MatchEntry[]
     */
    private $matches = [];

    /**
     * @param PatternId $patternId
     * @param \ReflectionClass $reflection
     * @param array $parents
     * @return MatchResultsCollector
     */
    public function addExtendsMatch(PatternId $patternId, \ReflectionClass $reflection, array $parents ): MatchResultsCollector
    {
        $matches = new MatchEntry($patternId);
        $matches->class = $reflection;
        $matches->extends = $parents;

        $this->matches[] = $matches;
    }

    /**
     * @param PatternId $patternId
     * @param \ReflectionParameter $reflection
     * @return MatchResultsCollector
     */
    public function addMatchParameters(PatternId $patternId, \ReflectionParameter $reflection): MatchResultsCollector
    {
        $matches = new MatchEntry($patternId);
        $matches->param = $reflection;

        $this->matches[] = $matches;
        return $this;
    }

    /**
     * @param PatternId $patternId
     * @param \ReflectionMethod $reflection
     * @return MatchResultsCollector
     */
    public function addMatchMethod(PatternId $patternId, \ReflectionMethod $reflection): MatchResultsCollector
    {
        $matches = new MatchEntry($patternId);
        $matches->method = $reflection;

        $this->matches[] = $matches;
        return $this;
    }

    public function addMatchClass(PatternId $patternId, \ReflectionClass $reflection) : MatchResultsCollector
    {
        $matches = new MatchEntry($patternId);
        $matches->class = $reflection;

        $this->matches[] = $matches;
        return $this;
    }

    public function addMatchConstant(PatternId $patternId, \ReflectionClassConstant $reflection) : MatchResultsCollector
    {
        $matches = new MatchEntry($patternId);
        $matches->constant = $reflection;

        $this->matches[] = $matches;
        return $this;
    }

    /**
     * @param array|PatternId[] $patterns
     * @param int|null $from
     * @return MatchResultsCollector
     */
    public function remove(array $patterns, int $from = null) : MatchResultsCollector
    {
        if (empty($this->matches)) {
            return $this;
        }

        $filter = function (MatchEntry $entry) use($patterns) {
            return !PatternId::inArray($entry->patternId, $patterns);
        };

        /** @var MatchEntry[] $keep */
        $keep = null;
        if ($from && $from > 0 && $from <= $this->count()) {
            $keep = array_splice($this->matches, 0, $from);
        } else {
            $keep = [];
        }

        $filtered = array_filter($this->matches, $filter);
        $this->matches = array_merge($keep, $filtered);

        return $this;
    }

    public function count()
    {
        return count($this->matches);
    }

    public function list(): MatchResults
    {
        return new MatchResults($this->matches);
    }
}