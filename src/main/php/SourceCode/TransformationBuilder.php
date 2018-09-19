<?php namespace Motorphp\SilexTools\Generators;

use Motorphp\SilexTools\ClassPattern\PatternGroup;
use Motorphp\SilexTools\ClassPattern\Pattern;

class TransformationBuilder
{
    /**
     * @var string
     */
    private $matchKey;

    /**
     * @var PatternGroup
     */
    private $expression;

    /** @var Callback */
    private $transform;

    public function __construct(string $matchKey, PatternGroup $expression)
    {
        $this->matchKey = $matchKey;
        $this->expression = $expression;
    }

    public function transform($callback): TransformationBuilder
    {
        $this->transform = $callback;
        return $this;
    }

    public function and(Transformer $transformer): Transformer
    {
        $transformer->add($this->matchKey, $this->expression, $this->transform);
        return $transformer;
    }

    public function done(Transformer $transformer): Transformer
    {
        $this->and($transformer);
        return $transformer;
    }
}