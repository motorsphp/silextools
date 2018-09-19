<?php namespace Motorphp\SilexTools\Matcher;

use Motorphp\SilexTools\ClassPattern\Match;
use Motorphp\SilexTools\ClassPattern\PatternId;

class MatchFactory
{
    /**
     * @var TypeAcceptor
     */
    private $typeAcceptor;

    /**
     * @var array
     */
    private $matchKeys;

    /**
     * MatchFactory constructor.
     * @param TypeAcceptor $typeAcceptor
     * @param array $matchKeys
     */
    public function __construct(TypeAcceptor $typeAcceptor, array $matchKeys)
    {
        $this->typeAcceptor = $typeAcceptor;
        $this->matchKeys = $matchKeys;
    }

    public function createMatch(PatternId $patternId, \Reflector $reflector)
    {
        if ($this->typeAcceptor->accepts($reflector)) {
            return new Match($patternId, $this->typeAcceptor->getType(), $reflector, $this->matchKeys);
        }

        throw new \DomainException('match factory does not apply to this type');
    }
}