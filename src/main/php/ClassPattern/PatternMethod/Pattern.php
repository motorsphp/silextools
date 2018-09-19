<?php namespace Motorphp\SilexTools\ClassPattern\PatternMethod;

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

    /**
     * @var int
     */
    private $modifiers;

    public function __construct(
        PatternId $id,
        MatchPolicyAnnotations $annotations = null,
        int $visibility = 0,
        int $modifiers = 0
    ) {
        $this->id = $id;
        $this->annotations = $annotations;
        $this->visibility = $visibility;
        $this->modifiers = $modifiers;
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

    public function getModifiers(): ?int
    {
        return $this->modifiers;
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
        $requirements = $context->beginMatch(\ReflectionMethod::class, $this->id->asString());

        if ($this->annotations) {
            $context->buildRequirement(
                $requirements
                    ->annotations($this->getAnnotations(), $this->getAnnotationsMatchPolicy())
                    ->setAppliesTo(\ReflectionMethod::class)
            );
        }

        if ($this->visibility) {
            $context->buildRequirement(
                $requirements
                    ->visibility($this->visibility)
                    ->setAppliesTo(\ReflectionClassConstant::class)
            );
        }

        if ($this->modifiers) {
            $context->buildRequirement(
                $requirements
                    ->modifiers($this->modifiers)
                    ->setAppliesTo(\ReflectionMethod::class)
            );
        }

        $context->endMatch();
    }
}
