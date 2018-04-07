<?php namespace Motorphp\SilexTools\Matcher;

use Motorphp\SilexTools\ClassPattern\PatternId;

/**
 * Internal class, used to store matches
 *
 * Class MatchEntry
 * @package Motorphp\SilexTools\Matcher
 */
class MatchEntry
{
    /**
     * @var PatternId
     */
    public $patternId;

    /**
     * @var \ReflectionClass
     */
    public $class;

    /**
     * @var \ReflectionMethod
     */
    public $method;

    /**
     * @var \ReflectionClassConstant
     */
    public $constant;

    /**
     * @var \ReflectionParameter
     */
    public $param;


    public function __construct(PatternId $patternId)
    {
        $this->patternId = $patternId;
    }
}