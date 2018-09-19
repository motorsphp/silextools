<?php namespace Motorphp\SilexTools\ClassPattern\PatternConstant;

use Motorphp\SilexTools\ClassPattern\Constraints\ConstraintsList;
use Motorphp\SilexTools\ClassPattern\Constraints\One;
use Motorphp\SilexTools\ClassPattern\MatchContext;
use Motorphp\SilexTools\ClassPattern\MatcherBuilder;
use Motorphp\SilexTools\ClassPattern\MatcherFactories;
use Motorphp\SilexTools\ClassPattern\MatchPolicyAnnotations;
use Motorphp\SilexTools\ClassPattern\PatternId;

class Pattern implements \Motorphp\SilexTools\ClassPattern\Pattern
{
    /**
     * @var PatternId
     */
    private $id;

    /**
     * @var MatchPolicyAnnotations
     */
    private $annotations;

    /**
     * @var int
     */
    private $visibility;

    public function __construct(PatternId $id, MatchPolicyAnnotations $annotations = null, int $visibility = 0)
    {
        $this->id = $id;
        $this->annotations = $annotations;
        $this->visibility = $visibility;
    }

    /**
     * @return PatternId
     */
    public function getId(): PatternId
    {
        return $this->id;
    }

    public function getAnnotationsMatchPolicy() : string
    {
        if ($this->annotations) {
            return $this->annotations->getPolicy();
        }

        return MatchPolicyAnnotations::MATCH_IGNORE;
    }

    /**
     * @return array|string[]
     */
    public function getAnnotations() : array
    {
        if ($this->annotations) {
            return $this->annotations->getAnnotations();
        }

        return [];
    }

    /**
     * @return int
     */
    public function getVisibility() : int
    {
        return $this->visibility;
    }

    function configureMatchContext(MatchContext $context)
    {
        $requirements = $context->beginMatch(\ReflectionClassConstant::class, $this->id->asString());

        if ($this->annotations) {
            $context->buildRequirement(
                $requirements
                    ->annotations($this->getAnnotations(), $this->getAnnotationsMatchPolicy())
                    ->setAppliesTo(\ReflectionClassConstant::class)
            );
        }

        if ($this->visibility) {
            $context->buildRequirement(
                $requirements
                    ->visibility($this->visibility)
                    ->setAppliesTo(\ReflectionClassConstant::class)
            );
        }

        $context->endMatch();
    }
}
