<?php namespace Motorphp\SilexTools\Matcher\MatchName;

use Motorphp\SilexTools\ClassPattern\Match;
use Motorphp\SilexTools\ClassPattern\PatternId;
use Motorphp\SilexTools\Matcher\MatchFactory;

class Test implements \Motorphp\SilexTools\Matcher\Test
{
    /**
     * @var string
     */
    private $namePattern;

    /**
     * MatcherImplements constructor.
     * @param PatternId $patternId
     * @param string $namePattern
     * @param MatchFactory $matchFactory
     */
    public function __construct($namePattern)
    {
        $this->namePattern = $namePattern;
    }

    public function test(\Reflector $reflector): bool
    {
        $name = null;
        if ($reflector instanceof \ReflectionClass) {
            $name = $reflector->getName();
        } else if ($reflector instanceof \ReflectionMethod) {
            $name = $reflector->getName();
        } else if ($reflector instanceof \ReflectionClassConstant) {
            $name = $reflector->getName();
        }

        if (! $name) {
            return false;
        }

        if ($this->namePattern === '*') {
            return true;
        }

        return false;
    }
}
