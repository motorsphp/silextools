<?php namespace Motorphp\SilexTools\ClassPattern\Matches;

class PatternMatches
{
    /** @var string */
    private $patternId;
    
    /** @var array| \ReflectionClass[]  */
    private $classes;

    /** @var array| \ReflectionMethod[]  */
    private $methods;

    /** @var array| \ReflectionParameter[]  */
    private $params;

    /** @var array| \ReflectionClassConstant[]  */
    private $constants;

    public function __construct( string $patternId )
    {
        $this->patternId = $patternId;
    }

    public function map(\Closure $closure, string $type) : array
    {
        switch ($type) {
            case \ReflectionMethod::class:
                return array_map($closure, $this->methods);
            case \ReflectionClass::class:
                return array_map($closure, $this->classes);
            case \ReflectionParameter::class:
                return array_map($closure, $this->params);
            case \ReflectionClassConstant::class:
                return array_map($closure, $this->constants);
            default:
                throw new \BadMethodCallException("unsupported type: $type");
        }
    }

    public function each(\Closure $closure, string $type)
    {
        switch ($type) {
            case \ReflectionMethod::class:
                array_walk($this->methods, $closure);
                break;
            case \ReflectionClass::class:
                array_walk($this->classes, $closure);
                break;
            case \ReflectionParameter::class:
                array_walk($this->params, $closure);
                break;
            case \ReflectionClassConstant::class:
                array_walk($this->constants, $closure);
                break;
        }
    }

    /**
     * @return string
     */
    public function getPatternId(): string
    {
        return $this->patternId;
    }

    public function isEmpty()
    {
        return true
            && empty($this->classes)
            && empty($this->methods)
            && empty($this->params)
            && empty($this->constants)
        ;
    }

    /**
     * @return array|\ReflectionClass[]
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * @param array|\ReflectionClass[] $classes
     */
    public function setClasses($classes): void
    {
        $this->classes = $classes;
    }

    /**
     * @return array|\ReflectionMethod[]
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @param array|\ReflectionMethod[] $methods
     */
    public function setMethods($methods): void
    {
        $this->methods = $methods;
    }

    /**
     * @return array|\ReflectionParameter[]
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array|\ReflectionParameter[] $params
     */
    public function setParams($params): void
    {
        $this->params = $params;
    }

    /**
     * @return array|\ReflectionClassConstant[]
     */
    public function getConstants()
    {
        return $this->constants;
    }

    /**
     * @param array|\ReflectionClassConstant[] $constants
     */
    public function setConstants($constants): void
    {
        $this->constants = $constants;
    }
}