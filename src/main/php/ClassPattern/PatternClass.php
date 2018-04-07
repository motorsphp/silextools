<?php namespace Motorphp\SilexTools\ClassPattern;

class PatternClass implements Pattern
{
    private $id;

    /**
     * @var array|string[]
     */
    private $interfaces = [];

    /**
     * @var array|string[]
     */
    private $parents = [];

    /**
     * @var array|string[]
     */
    private $methodAnnotations = [];

    /**
     * @var array|string[]
     */
    private $classAnnotations = [];

    /**
     * ExpressionClass constructoructor.
     *
     * @param PatternId $id
     * @param array $interfaces
     * @param array $parents
     * @param array $methodAnnotations
     * @param array $classAnnotations
     */
    public function __construct(PatternId $id, array $interfaces, array $parents, array $methodAnnotations, array $classAnnotations)
    {
        $this->id = $id;
        $this->interfaces = $interfaces;
        $this->parents = $parents;
        $this->methodAnnotations = $methodAnnotations;
        $this->classAnnotations = $classAnnotations;
    }

    public function getId() : PatternId
    {
        return $this->id;
    }

    /**
     * @return array|string[]
     */
    public function getInterfaces() : array
    {
        return $this->interfaces;
    }

    /**
     * @return array|string[]
     */
    public function getParents() : array
    {
        return $this->parents;
    }

    /**
     * @return array|string[]
     */
    public function getMethodAnnotations() : array
    {
        return $this->methodAnnotations;
    }

    /**
     * @return array|string[]
     */
    public function getClassAnnotations() : array
    {
        return $this->classAnnotations;
    }

    public function configureMatcher(MatcherConfigurator $visitor)
    {
        if (count($this->interfaces)) {
            $visitor->addImplements($this);
        }

        if (count($this->classAnnotations)) {
            $visitor->addAnnotationsClass($this);
        }

        if (count($this->methodAnnotations)) {
            $visitor->addAnnotationsClassMethod($this);
        }
    }
}
