<?php namespace Motorphp\SilexTools\Matcher;

use Motorphp\SilexAnnotations\Reader\ConstantsReader;
use Motorphp\SilexTools\ClassPattern\PatternId;

class MatcherAnnotations implements MatcherClass, MatcherMethod, MatcherConstant
{
    /**
     * @var PatternId
     */
    private $patternId;

    /**
     * @var string[]|array
     */
    private $annotations;

    /**
     * @var ConstantsReader
     */
    private $reader;

    /**
     * @var string
     */
    private $matchType;

    public function __construct(PatternId $id, array $annotations, string $matchType, ConstantsReader $reader)
    {
        $this->patternId = $id;
        $this->annotations = $annotations;
        $this->reader = $reader;
        $this->matchType = $matchType;
    }

    public function getPatternId(): PatternId
    {
        return $this->patternId;
    }

    public function matchMethod(\ReflectionMethod $reflection, MatchResultsCollector $matches) : bool
    {
        $annotations = [];
        foreach ($this->annotations as $annotation) {
            $annotationObject = $this->reader->getMethodAnnotation($reflection, $annotation);
            if (!is_null($annotationObject)) {
                $annotations[] = $annotationObject;
            }
        }

        $isMatch = $this->isMatch($annotations);
        if ($isMatch) {
            $matches->addMatchMethod($this->patternId, $reflection);
        }

        return $isMatch;
    }

    public function matchClass(\ReflectionClass $reflection, MatchResultsCollector $matches) : bool
    {
        $annotations = [];
        foreach ($this->annotations as $annotation) {
            $annotationObject = $this->reader->getClassAnnotation($reflection, $annotation);
            if (!is_null($annotationObject)) {
                $annotations[] = $annotationObject;
            }
        }

        $isMatch = $this->isMatch($annotations);
        if ($isMatch) {
            $matches->addMatchClass($this->patternId, $reflection);
        }

        return $isMatch;
    }

    public function matchConstant(\ReflectionClassConstant $reflection, MatchResultsCollector $matches) : bool
    {
        $annotations = [];
        foreach ($this->annotations as $annotation) {
            $annotationObject = $this->reader->getConstantAnnotation($reflection, $annotation);
            if (!is_null($annotationObject)) {
                $annotations[] = $annotationObject;
            }
        }

        $isMatch = $this->isMatch($annotations);
        if ($isMatch) {
            $matches->addMatchConstant($this->patternId, $reflection);
        }

        return $isMatch;
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