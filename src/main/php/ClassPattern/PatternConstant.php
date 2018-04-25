<?php namespace Motorphp\SilexTools\ClassPattern;

class PatternConstant implements Pattern
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

    public function configureMatcher(MatcherConfigurator $builder)
    {
        if ($this->annotations) {
            $builder->addAnnotationsConstant($this);
        }

        if ($this->visibility) {
            $builder->addVisibilityConstant($this);
        }
    }
}
