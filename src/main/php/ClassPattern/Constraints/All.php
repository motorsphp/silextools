<?php namespace Motorphp\SilexTools\ClassPattern\Constraints;

use Motorphp\SilexTools\ClassPattern\MatchResults;

class All implements Constraint
{
    /**
     * @var string
     */
    private $from;

    /**
     * @var array
     */
    private $to;

    /**
     * @param string $from
     * @param array $to
     */
    public function __construct(string $from, array $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @param MatchResults $results
     * @return bool
     */
    function isSatisfied(MatchResults $results): bool
    {
        return false;
    }

    /**
     * @return string
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * @return string
     */
    public function getTo(): array
    {
        return $this->to;
    }
}