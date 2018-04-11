<?php namespace Motorphp\SilexTools\ClassPattern;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\AnnotationReader;

class PatternBuilder
{
    /**
     * @var string
     */
    private $name = '';

    /**
     * @var array|PatternMethod[]
     */
    private $methods = [];

    /**
     * @var array|PatternConstant[]
     */
    private $constants = [];

    /**
     * @var PatternClass
     */
    private $class;

    /**
     * @var Reader
     */
    private $reader;

    public static function copy(Expression $pattern, Reader $reader): PatternBuilder
    {
        $builder = new PatternBuilder($reader);
        $name = $pattern->getName();
        $builder->setName($name);

        $methods = $pattern->getMethods();
        foreach ($methods as $method) {
            $builder->method($method);
        }

        $class = $pattern->getClass();
        if ($class) {
            $builder->class($class);
        }

        $constants = $pattern->getConstants();
        foreach ($constants as $constant) {
            $builder->constant($constant);
        }

        return $builder;
    }

    /**
     * MatcherFactories constructor.
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    public function constant(PatternConstant $pattern): PatternBuilder
    {
        $this->constants[] = $pattern;
        return $this;
    }

    public function constantPattern(string $key): PatternBuilderConstant
    {
        $pattern = $this->build();
        return new PatternBuilderConstant($key, $this->reader, $pattern);
    }

    public function method(PatternMethod $method): PatternBuilder
    {
        $this->methods[] = $method;
        return $this;
    }

    public function methodPattern(string $key): PatternBuilderMethod
    {
        $pattern = $this->build();
        return new PatternBuilderMethod($key, $this->reader, $pattern);
    }

    public function class(PatternClass $class): PatternBuilder
    {
        $this->class = $class;
        return $this;
    }

    public function classPattern(string $key): PatternBuilderClass
    {
        $pattern = $this->build();
        return new PatternBuilderClass($key, $this->reader, $pattern);
    }

    public function build() : Expression
    {
        return new Expression($this->name, $this->methods, $this->constants, $this->class);
    }
}