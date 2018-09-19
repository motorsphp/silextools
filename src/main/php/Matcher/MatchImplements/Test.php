<?php namespace Motorphp\SilexTools\Matcher\MatchImplements;

use Motorphp\SilexTools\ClassPattern\Match;
use Motorphp\SilexTools\ClassPattern\PatternId;
use Motorphp\SilexTools\Matcher\MatchFactory;

class Test implements \Motorphp\SilexTools\Matcher\Test
{
    /**
     * @var array|string[]
     */
    private $interfaces;

    public function __construct(array $interfaces)
    {
        $this->interfaces = $interfaces;
    }

    public function test(\Reflector $reflector): bool
    {

        if ($reflector instanceof \ReflectionClass) {
            $matched = [];
            foreach ($this->interfaces as $interface) {
                if ($reflector->implementsInterface($interface)) {
                    $matched[] = $interface;
                }
            }

            return 0 < count($matched);
        }

        return false;
    }
}
