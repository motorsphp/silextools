<?php namespace Motorphp\SilexTools\ClassPattern;

class PatternMethod implements Pattern
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

    /**
     * @var int
     */
    private $modifiers;

    public function __construct(PatternId $id, array $annotations, int $visibility = 0, int $modifiers = 0)
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

    /**
     * @return array|string[]
     */
    public function getAnnotations() : array
    {
        return $this->annotations;
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
        if (count($this->annotations)) {
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
