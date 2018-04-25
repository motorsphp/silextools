<?php namespace Motorphp\SilexTools\ClassPattern;

class PatternMethod implements Pattern
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

    public function __construct(PatternId $id, MatchPolicyAnnotations $annotations = null, int $visibility = 0, int $modifiers = 0)
    {
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

    public function configureMatcher(MatcherConfigurator $collector)
    {
        if ($this->annotations) {
            $collector->addAnnotationsMethod($this);
        }

        if ($this->visibility) {
            $collector->addVisibilityMethod($this);
        }

        if ($this->modifiers) {
            $collector->addModifiersMethod($this);
        }
    }
}
