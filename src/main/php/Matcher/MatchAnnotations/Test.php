<?php namespace Motorphp\SilexTools\Matcher\MatchAnnotations;

use Motorphp\SilexAnnotations\Reader\ConstantsReader;

class Test implements \Motorphp\SilexTools\Matcher\Test
{
    /**
     * @var string[]|array
     */
    private $annotations;

    /**
     * @var string
     */
    private $matchType;

    /**
     * @var Reader
     */
    private $reader;

    public static function instance(array $annotations, string $matchType, ConstantsReader $reader) : Test
    {
       return new Test($annotations, $matchType, new Reader($reader));
    }

    /**
     * Test constructor.
     * @param array|string[] $annotations
     * @param string $matchType
     * @param Reader $reader
     */
    public function __construct(array $annotations, string $matchType, Reader $reader)
    {
        $this->annotations = $annotations;
        $this->matchType = $matchType;
        $this->reader = $reader;
    }

    function test(\Reflector $reflector): bool
    {
        $annotations = [];
        foreach ($this->annotations as $annotation) {
            $annotationObject = $this->reader->read($reflector, $annotation);
            if (!is_null($annotationObject)) {
                $annotations[] = $annotationObject;
            }
        }

        return $this->isMatch($annotations);
    }

    private function isMatch(array $annotations) : bool
    {
        if ($this->matchType === 'all' && count($this->annotations) === count($annotations)) {
            return true;
        }

        if ($this->matchType === 'any' && !empty($annotations)) {
            return true;
        }

        return false;
    }
}