<?php namespace Motorphp\SilexTools\ClassPattern\Constraints;

use Motorphp\SilexTools\ClassPattern\MatchResults;

interface Constraint
{
    /**
     * @param MatchResults $results
     * @return bool
     */
    function isSatisfied(MatchResults $results): bool;

    /**
     * @return string
     */
    public function getFrom(): string;

//    /**
//     * @return string
//     */
//    public function getTo(): string;
}