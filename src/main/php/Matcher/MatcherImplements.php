<?php namespace Motorphp\SilexTools\Matcher;

use Motorphp\SilexTools\ClassPattern\PatternId;

class MatcherImplements implements MatcherClass
{
    /**
     * @var PatternId
     */
    private $patternId;

    /**
     * @var array|string[]
     */
    private $interfaces;

    /**
     * MatcherImplements constructor.
     * @param PatternId $patternId
     * @param array|string[] $interfaces
     */
    public function __construct(PatternId $patternId, array $interfaces)
    {
        $this->patternId = $patternId;
        $this->interfaces = $interfaces;
    }

    public function getPatternId(): PatternId
    {
        return $this->patternId;
    }

    public function matchClass(\ReflectionClass $reflector, MatchResultsCollector $matches): bool
    {
        $matched = [];
        foreach ($this->interfaces as $interface) {
            if ($reflector->implementsInterface($interface)) {
                $matched[] = $interface;
            }
        }

        if (count($matched)) {
            $matches->addMatchClass($this->patternId, $reflector);
            return true;
        }
        return false;
    }


}
