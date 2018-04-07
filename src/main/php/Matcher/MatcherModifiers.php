<?php namespace Motorphp\SilexTools\Matcher;

use Motorphp\SilexTools\ClassPattern\Constants;
use Motorphp\SilexTools\ClassPattern\PatternId;

class MatcherModifiers implements  MatcherClass, MatcherMethod
{
    /**
     * @var int
     */
    private $modifiers;
    /**
     * @var PatternId
     */
    private $patternId;

    public function __construct(PatternId $id, int $modifiers = 0)
    {
        $this->patternId = $id;
        $this->modifiers = $modifiers;
    }

    public function getPatternId(): PatternId
    {
        return $this->patternId;
    }

    public function matchClass(\ReflectionClass $reflector, MatchResultsCollector $matches): bool
    {
        $isMatch = false;
        if (Constants::MODIFIER_ABSTRACT & $this->modifiers && $reflector->isAbstract()) {
            $isMatch = true;
        }
        if (Constants::MODIFIER_FINAL & $this->modifiers && $reflector->isFinal()) {
            $isMatch = true;
        }

        if ($isMatch) {
            $matches->addMatchClass($this->patternId, $reflector);
        }

        return $isMatch;
    }

    public function matchMethod(\ReflectionMethod $reflector, MatchResultsCollector $matches): bool
    {
        $isMatch = false;
        if (Constants::MODIFIER_STATIC & $this->modifiers && $reflector->isStatic()) {
            $isMatch = true;
        }
        if (Constants::MODIFIER_FINAL & $this->modifiers && $reflector->isFinal()) {
            $isMatch = true;
        }
        if (Constants::MODIFIER_ABSTRACT & $this->modifiers && $reflector->isAbstract()) {
            $isMatch = true;
        }

        if ($isMatch) {
            $matches->addMatchMethod($this->patternId, $reflector);
        }

        return $isMatch;
    }
}