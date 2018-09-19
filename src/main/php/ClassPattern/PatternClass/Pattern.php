<?php namespace Motorphp\SilexTools\ClassPattern\PatternClass;

use Motorphp\SilexTools\ClassPattern\Constraints\All;
use Motorphp\SilexTools\ClassPattern\Constraints\ConstraintsList;
use Motorphp\SilexTools\ClassPattern\MatchContext;
use Motorphp\SilexTools\ClassPattern\MatcherBuilder;
use Motorphp\SilexTools\ClassPattern\MatcherFactories;
use Motorphp\SilexTools\ClassPattern\MatchPolicyAnnotations;
use Motorphp\SilexTools\ClassPattern\PatternId;

class Pattern implements \Motorphp\SilexTools\ClassPattern\Pattern
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
    public function __construct(
        PatternId $id,
        array $interfaces,
        array $parents,
        array $methodAnnotations,
        array $classAnnotations
    ) {
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

    function configureMatchContext(MatchContext $context)
    {
        $requirements = $context->beginMatch(\ReflectionClass::class, $this->id->asString());

        if (count($this->interfaces)) {
            $context->buildRequirement(
                $requirements->implements($this->interfaces)
            );
        }

        if (count($this->classAnnotations)) {
            $context->buildRequirement(
                $requirements
                    ->annotations($this->classAnnotations, MatchPolicyAnnotations::MATCH_ANY)
                    ->setAppliesTo(\ReflectionClass::class)
            );
        }

        if (count($this->methodAnnotations)) {
            $context->buildRequirement(
                $requirements
                    ->annotations($this->methodAnnotations, MatchPolicyAnnotations::MATCH_ALL)
                    ->setAppliesTo(\ReflectionMethod::class)
            );
        }

        $context->endMatch();
    }
}
