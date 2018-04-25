<?php namespace Motorphp\SilexTools\ClassPattern;

class MatchPolicyAnnotations
{
    const MATCH_IGNORE = 'ignore';

    const MATCH_ANY = 'any';

    const MATCH_ALL = 'all';

    /** @var array|string[] */
    private $annotations;

    /** @var string */
    private $policy;

    public static function matchAny(array $annotations) : MatchPolicyAnnotations
    {
        return new MatchPolicyAnnotations($annotations, MatchPolicyAnnotations::MATCH_ANY);
    }

    public static function matchAll(array $annotations) : MatchPolicyAnnotations
    {
        return new MatchPolicyAnnotations($annotations, MatchPolicyAnnotations::MATCH_ALL);
    }

    public function __construct(array $annotations, string $policy)
    {
        $this->annotations = $annotations;
        $this->policy = $policy;
    }

    /**
     * @return array|string[]
     */
    public function getAnnotations()
    {
        return $this->annotations;
    }

    /**
     * @return string
     */
    public function getPolicy(): string
    {
        return $this->policy;
    }
}