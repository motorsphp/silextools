<?php namespace Motorphp\SilexTools\ClassPattern;

class Expression
{
    /**
     * @var string name of the pattern
     */
    private $name;

    /**
     * @var array|PatternMethod[]
     */
    private $methods = [];

    /**
     * @var array|PatternConstant[]
     */
    private $constants = [];

    /**
     * @var PatternClass|null
     */
    private $class;

    public function __construct(string $name, array $methods, array $constants, ?PatternClass $class = null)
    {
        $this->name = $name;
        $this->methods = $methods;
        $this->constants = $constants;
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array|PatternMethod[]
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @return array|PatternConstant[]
     */
    public function getConstants()
    {
        return $this->constants;
    }

    /**
     * @return PatternClass
     */
    public function getClass(): ?PatternClass
    {
        return $this->class;
    }

    public function configureMatcher(MatcherConfigurator $matchers)
    {
        if ($this->class) {
            $this->class->configureMatcher($matchers);
        }

        foreach ($this->methods as $method) {
            $method->configureMatcher($matchers);
        }

        foreach ($this->constants as $constant) {
            $constant->configureMatcher($matchers);
        }
    }
}