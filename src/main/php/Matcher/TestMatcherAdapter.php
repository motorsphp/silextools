<?php namespace Motorphp\SilexTools\Matcher;

use Motorphp\SilexTools\ClassPattern\Constraints;
use Motorphp\SilexTools\ClassPattern\PatternId;
use Motorphp\SilexTools\ClassPattern\Matcher;
use Motorphp\SilexTools\ClassPattern\Match;

class TestMatcherAdapter implements Matcher
{
    /**
     * @var Test
     */
    private $tester;

    /**
     * @var MatchFactory
     */
    private $matchFactory;

    /**
     * @var TypeAcceptor
     */
    private $appliesToType;

    /**
     * @var array|Constraints\Constraint[]
     */
    private $constraints;

    /**
     * TestMatcherAdapter constructor.
     * @param Test $tester
     * @param MatchFactory $matchFactory
     * @param TypeAcceptor $appliesToType
     */
    public function __construct(Test $tester, MatchFactory $matchFactory, TypeAcceptor $appliesToType)
    {
        $this->tester = $tester;
        $this->matchFactory = $matchFactory;
        $this->appliesToType = $appliesToType;
    }

    public function appliesTo(string $reflectorType) : bool
    {
        return $this->appliesToType->getType() === $reflectorType;
    }

    public function match(\Reflector $reflector): ?Match
    {
        if (!$this->appliesToType->accepts($reflector)) {
            throw new \DomainException('matcher does not apply to this matcher type');
        }

        if ($this->tester->test($reflector)) {
            return $this->matchFactory->createMatch($this->patternId, $reflector);
        }

        return null;
    }
}