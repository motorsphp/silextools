<?php namespace Motorphp\SilexTools\ClassPattern;

class PatternConstant implements Pattern
{
    /**
     * @var PatternId
     */
    private $id;

    /**
     * @var array|string[]
     */
    private $annotations;

    /**
     * @var int
     */
    private $visibility;

    public function __construct(PatternId $id, array $annotations, int $visibility = 0)
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

    /**
     * @return array|string[]
     */
    public function getAnnotations() : array
    {
        return $this->annotations;
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
        if (count($this->annotations)) {
            $builder->addAnnotationsConstant($this);
        }

        if ($this->visibility) {
            $builder->addVisibilityConstant($this);
        }
    }
}
