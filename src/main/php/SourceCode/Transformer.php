<?php namespace Motorphp\SilexTools\Generators;

use Motorphp\SilexAnnotations\Reader\ConstantsReader;
use Motorphp\SilexTools\ClassPattern\PatternGroup;
use Motorphp\SilexTools\ClassPattern\PatternMatches;
use Motorphp\SilexTools\ClassPattern\PatternGroupBuilder;
use Motorphp\SilexTools\ClassPattern\PatternClass;
use Motorphp\SilexTools\ClassPattern\PatternConstant;
use Motorphp\SilexTools\ClassPattern\PatternMethod;
use Motorphp\SilexTools\ClassScanner\Scanner;
use Motorphp\SilexTools\Matcher\Factories;
use Motorphp\SilexTools\Matcher\Matcher;
use Motorphp\SilexTools\Matcher\Matches;

class Transformer
{
    /**
     * @var ConstantsReader
     */
    private $reader;

    /**
     * @var array|Matcher[]
     */
    private $matchers;

    /**
     * @var array
     */
    private $transformations;

    private $id = 0;

    public function __construct(ConstantsReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param string $matchKey
     * @param PatternGroup $pattern
     * @param \Closure $transformer
     * @return Transformer
     */
    public function add(string $matchKey, PatternGroup $pattern, $transformer): Transformer
    {
        $builder = new Factories($this->reader);
        foreach ($pattern->getMatchers($builder) as $matcher) {
            $this->matchers[] = $matcher->build();
        }

        $this->transformations[$matchKey] = $transformer;
        return $this;
    }

    public function run(array $sources, Scanner $scanner)
    {
        $classes = iterator_to_array($scanner->iterateAllClasses($sources));
        $matches = Matches::select($classes, $this->matchers);

        foreach (array_keys($this->transformations) as $id) {
            $required = $matches->filterByKey($id);

            $transformer = $this->transformations[$id];
            $transformer($required);
        }

        return $matches;
    }

    public function expression($callback): TransformationBuilder
    {
        $this->id++;
        $matchKey = $this->id;

        $builder = new PatternGroupBuilder($this->reader);
        $builder->addMatchKey($matchKey);
        $callback($builder);

        $pattern = $builder->build();
        return new TransformationBuilder($matchKey, $pattern);
    }

    /**
     * @param \Closure $callback
     * @return TransformationBuilder
     */
    public function constantPattern($callback): TransformationBuilder
    {
        $this->id++;
        $matchKey = $this->id;

        $builder = new PatternConstant\Builder($this->reader);
        $callback($builder);

        $builderExpression = new PatternGroupBuilder($this->reader);
        $expression = $builderExpression
            ->addPattern($builder->build())
            ->addMatchKey($matchKey)
            ->build()
        ;

        return new TransformationBuilder($matchKey, $expression);
    }

    /**
     * @param \Closure $callback
     * @return TransformationBuilder
     */
    public function classPattern($callback): TransformationBuilder
    {
        $this->id++;
        $matchKey = $this->id;

        $builder = new PatternClass\Builder($this->reader);
        $callback($builder);

        $builderExpression = new PatternGroupBuilder($this->reader);
        $expression = $builderExpression
            ->addPattern($builder->build())
            ->addMatchKey($matchKey)
            ->build()
        ;

        return new TransformationBuilder($matchKey, $expression);
    }

    /**
     * @param \Closure $callback
     * @return TransformationBuilder
     */
    public function methodPattern($callback): TransformationBuilder
    {
        $this->id++;
        $matchKey = $this->id;

        $builder = new PatternMethod\Builder($this->reader);
        $callback($builder);

        $builderExpression = new PatternGroupBuilder($this->reader);
        $expression = $builderExpression
            ->addPattern($builder->build())
            ->addMatchKey($matchKey)
            ->build()
        ;

        return new TransformationBuilder($matchKey, $expression);
    }

}