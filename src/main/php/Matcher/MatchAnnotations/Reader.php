<?php namespace Motorphp\SilexTools\Matcher\MatchAnnotations;

use Motorphp\SilexAnnotations\Reader\ConstantsReader;

class Reader
{
    /**
     * @var ConstantsReader
     */
    private $reader;

    /**
     * AnnotationReader constructor.
     * @param ConstantsReader $reader
     */
    public function __construct(ConstantsReader $reader)
    {
        $this->reader = $reader;
    }

    public function read(\Reflector $reflector, string $annotation) {
        if ($reflector instanceof \ReflectionClass) {
            return $this->reader->getClassAnnotation($reflector, $annotation);
        }

        if ($reflector instanceof \ReflectionMethod) {
            return $this->reader->getMethodAnnotation($reflector, $annotation);
        }

        if ($reflector instanceof \ReflectionClassConstant) {
            return $this->reader->getConstantAnnotation($reflector, $annotation);
        }

        return null;
    }


}