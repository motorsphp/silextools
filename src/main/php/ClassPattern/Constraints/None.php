<?php namespace Motorphp\SilexTools\ClassPattern\Constraints;

use Motorphp\SilexTools\ClassPattern\MatchResults;

class None implements Constraint
{
    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $to;

    /**
     * @param string $from
     * @param string $to
     */
    public function __construct(string $from, string $to)
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
    public function getTo(): string
    {
        return $this->to;
    }
}