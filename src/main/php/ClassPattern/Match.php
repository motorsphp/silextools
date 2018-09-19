<?php namespace Motorphp\SilexTools\ClassPattern;

/**
 * Internal class, used to store matches
 *
 * Class MatchEntry
 */
class Match
{
    /**
     * @var PatternId
     */
    public $patternId;

    /**
     * @var string
     */
    public $type;

    /**
     * @var \Reflector
     */
    public $reflector;

    /**
     * @var array|string[]
     */
    public $matchKeys = [];

    public function __construct(PatternId $patternId, string $type, $reflector, array $matchKeys)
    {
        $this->patternId = $patternId;
        $this->type = $type;
        $this->reflector = $reflector;
        $this->matchKeys = $matchKeys;
    }
}