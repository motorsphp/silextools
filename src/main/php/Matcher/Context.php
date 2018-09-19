<?php namespace Motorphp\SilexTools\Matcher;

use Motorphp\SilexAnnotations\Reader\ConstantsReader;
use Motorphp\SilexTools\ClassPattern\MatchContext;
use Motorphp\SilexTools\ClassPattern\Matcher;
use Motorphp\SilexTools\ClassPattern\MatcherBuilder;
use Motorphp\SilexTools\ClassPattern\MatcherFactories;
use Symfony\Component\Validator\Constraints;

class Context implements MatchContext
{
    static private $matchId = 0;

    /** @var array|string[]  */
    private $children = [];

    /** @var array|string[] */
    private $parents = [];

    /** @var array|Matcher[]  */
    private $matchers = [];

    /**
     * @var array
     */
    private $constraints = [];

    /** @var ConstantsReader */
    private $reader;

    public function __construct()
    {
        $this->reader = new ConstantsReader();
    }

    /**
     * @param string $tokenType
     * @param string $label
     * @return MatcherFactories
     */
    function beginMatch(string $tokenType, string $label): MatcherFactories
    {
        $this->parents[] = [$label, count($this->children)];
        return new Factories($this->reader);
    }

    /**
     * Adds a Matcher to this context
     *
     * @param MatcherBuilder $matcher
     * @return string the match label
     */
    function buildRequirement(MatcherBuilder $matcher): string
    {
        foreach ($this->parents as $label) {
            $matcher->addMatchLabel($label);
        }

        $matchLabel = 'label-' . Context::$matchId++;
        $matcher->addMatchLabel($matchLabel);

        $this->matchers[] = $matcher->build();
        $this->addRequirement($matchLabel);

        return $matchLabel;
    }

    /**
     * Add a requirement on a existing match for the current one
     *
     * @param string $label
     * @return mixed
     */
    function addRequirement(string $label)
    {
        $this->children[] = $label;
    }

    /**
     * @return MatchContext
     */
    function endMatch(): MatchContext
    {
        $parent = array_pop($this->parents);

        list($label, $offset) = $parent;

        if ($offset < count($this->children)) {
            $requirements = array_splice($this->children, $offset + 1);
        } else {
            $requirements = [];
        }

        if (count($requirements)) {
            $this->constraints[] = new Constraints\All($label, $requirements);
        }
        $this->children[] = $label;

        return $this;
    }
}