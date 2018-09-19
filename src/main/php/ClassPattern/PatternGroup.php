<?php namespace Motorphp\SilexTools\ClassPattern;

class PatternGroup implements Pattern
{
    /** @var PatternId */
    private $id;

    /**
     * @var array|Pattern[]
     */
    private $patterns = [];

    /**
     * @var array
     */
    private $matchLabels = [];

    public function __construct(PatternId $id, array $patterns, array $matchLabels)
    {
        $this->id = $id;
        $this->patterns = $patterns;
        $this->matchLabels = $matchLabels;
    }

    function getId(): PatternId
    {
        return $this->id;
    }

    function configureMatchContext(MatchContext $context)
    {
        $context->beginMatch(\ReflectionClass::class, $this->id->asString());

        foreach ($this->patterns as $pattern) {
            $pattern->configureMatchContext($context);
        }

        // post-order for requirements: add parent requirements after the children requirements
        foreach ($this->patterns as $pattern) {
            $context->addRequirement($pattern->getId()->asString());
        }

        $context->endMatch();
    }
}