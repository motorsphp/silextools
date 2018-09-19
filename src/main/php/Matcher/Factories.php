<?php namespace Motorphp\SilexTools\Matcher;

use Motorphp\SilexAnnotations\Reader\ConstantsReader;
use Motorphp\SilexTools\ClassPattern\MatcherBuilder;
use Motorphp\SilexTools\ClassPattern\MatcherFactories;
use Motorphp\SilexTools\ClassPattern\PatternClass;
use Motorphp\SilexTools\ClassPattern\PatternConstant;
use Motorphp\SilexTools\ClassPattern\PatternId;
use Motorphp\SilexTools\ClassPattern\PatternMethod;

class Factories implements MatcherFactories
{
    /**
     * @var ConstantsReader
     */
    private $reader;

    public function __construct(ConstantsReader $reader)
    {
        $this->reader = $reader;
    }

    function visibility(int $visibility): MatcherBuilder
    {
        $test = MatchModifiers\Test::instance($visibility);
        $builder = new TestMatcherBuilder();
        $builder->setTest($test);

        return $builder;
    }

    function modifiers(int $modifiers): MatcherBuilder
    {
        $test = MatchModifiers\Test::instance($modifiers);
        $builder = new TestMatcherBuilder();
        $builder->setTest($test);

        return $builder;
    }

    function annotations(array $annotations, $matchType): MatcherBuilder
    {
        $test = MatchAnnotations\Test::instance($annotations, $matchType, $this->reader);
        $builder = new TestMatcherBuilder();
        $builder->setTest($test);

        return $builder;
    }

    function implements(array $interfaces): MatcherBuilder
    {
        $test = new MatchImplements\Test($interfaces);
        $builder = new TestMatcherBuilder();
        $builder->setTest($test)->setAppliesTo(\ReflectionClass::class);

        return $builder;
    }

    function anyClass(): MatcherBuilder
    {
        $test = new MatchName\Test('*');
        $builder = new TestMatcherBuilder();
        $builder->setTest($test)->setAppliesTo(\ReflectionClass::class);

        return $builder;
    }
}