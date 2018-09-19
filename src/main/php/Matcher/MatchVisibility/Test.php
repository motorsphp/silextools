<?php namespace Motorphp\SilexTools\Matcher\MatchVisibility;

use Motorphp\SilexTools\ClassPattern\Constants;

class Test implements \Motorphp\SilexTools\Matcher\Test
{
    /**
     * @var int
     */
    private $visibility;

    /**
     * @var Reader
     */
    private $reader;

    public static function instance(int $visibility) : Test
    {
        return new Test($visibility, new Reader());
    }

    /**
     * Test constructor.
     * @param int $visibility
     * @param Reader $reader
     */
    public function __construct(int $visibility, Reader $reader)
    {
        $this->visibility = $visibility;
        $this->reader = $reader;
    }

    function test(\Reflector $reflector): bool
    {
        $isMatch = false;
        if (Constants::VISIBILITY_PRIVATE & $this->visibility && $this->reader->isPrivate($reflector)) {
            $isMatch = true;
        }
        if (Constants::VISIBILITY_PROTECTED & $this->visibility && $this->reader->isProtected($reflector)) {
            $isMatch = true;
        }
        if (Constants::VISIBILITY_PUBLIC & $this->visibility && $this->reader->isPublic($reflector)) {
            $isMatch = true;
        }

        return $isMatch;
    }
}