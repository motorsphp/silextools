<?php namespace Motorphp\SilexTools\Matcher;

use Motorphp\SilexTools\ClassPattern\Constants;
use Motorphp\SilexTools\ClassPattern\PatternId;

class MatcherVisibility implements  MatcherConstant, MatcherMethod
{
    /**
     * @var int
     */
    private $visibility;

    /**
     * @var PatternId
     */
    private $patternId;

    public function __construct(PatternId $id, int $visibility = 0)
    {
        $this->patternId = $id;
        $this->visibility = $visibility;
    }

    public function getPatternId(): PatternId
    {
        return $this->patternId;
    }

    public function matchMethod(\ReflectionMethod $reflector, MatchResultsCollector $matches): bool
    {
        $isMatch = false;
        if (Constants::VISIBILITY_PRIVATE & $this->visibility && $reflector->isPrivate()) {
            $isMatch = true;
        }
        if (Constants::VISIBILITY_PROTECTED & $this->visibility && $reflector->isProtected()) {
            $isMatch = true;
        }
        if (Constants::VISIBILITY_PUBLIC & $this->visibility && $reflector->isPublic()) {
            $isMatch = true;
        }

        if ($isMatch) {
            $matches->addMatchMethod($this->patternId, $reflector);
        }

        return $isMatch;
    }

    public function matchConstant(\ReflectionClassConstant $reflector, MatchResultsCollector $matches): bool
    {
        $isMatch = false;
        if (Constants::VISIBILITY_PRIVATE & $this->visibility && $reflector->isPrivate()) {
            $isMatch = true;
        }
        if (Constants::VISIBILITY_PROTECTED & $this->visibility && $reflector->isProtected()) {
            $isMatch = true;
        }
        if (Constants::VISIBILITY_PUBLIC & $this->visibility && $reflector->isPublic()) {
            $isMatch = true;
        }

        if ($isMatch) {
            $matches->addMatchConstant($this->patternId, $reflector);
        }

        return $isMatch;
    }
}