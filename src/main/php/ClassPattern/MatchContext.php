<?php namespace Motorphp\SilexTools\ClassPattern;

interface MatchContext
{
    /**
     * @param string $tokenType
     * @param string $label
     * @return MatcherFactories
     */
    function beginMatch(string $tokenType, string $label) : MatcherFactories;

    /**
     * Adds a Matcher to this context
     *
     * @param MatcherBuilder $matcher
     * @return string the match label
     */
    function buildRequirement(MatcherBuilder $matcher) : string;

    /**
     * Add a requirement on a existing match for the current one
     *
     * @param string $label
     * @return mixed
     */
    function addRequirement(string $label);

    /**
     * @return MatchContext
     */
    function endMatch() : MatchContext;
}