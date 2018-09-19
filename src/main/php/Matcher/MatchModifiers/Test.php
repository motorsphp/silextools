<?php namespace Motorphp\SilexTools\Matcher\MatchModifiers;

use Motorphp\SilexTools\ClassPattern\Constants;

class Test implements \Motorphp\SilexTools\Matcher\Test
{
    /**
     * @var int
     */
    private $modifiers;

    /**
     * @var Reader
     */
    private $reader;

    public static function instance(int $modifiers) : Test
    {
        return new Test($modifiers, new Reader());
    }

    /**
     * Test constructor.
     * @param int $modifiers
     * @param Reader $reader
     */
    public function __construct(int $modifiers, Reader $reader)
    {
        $this->modifiers = $modifiers;
        $this->reader = $reader;
    }

    function test(\Reflector $reflector): bool
    {
        $isMatch = false;
        if (Constants::MODIFIER_STATIC & $this->modifiers && $this->reader->isStatic($reflector)) {
            $isMatch = true;
        }
        if (Constants::MODIFIER_ABSTRACT & $this->modifiers && $this->reader->isAbstract($reflector)) {
            $isMatch = true;
        }
        if (Constants::MODIFIER_FINAL & $this->modifiers && $this->reader->isFinal($reflector)) {
            $isMatch = true;
        }

        return $isMatch;
    }
}