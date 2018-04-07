<?php namespace Motorphp\SilexTools\ClassPattern;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\Reader;

class PatternBuilderMethod
{
    /**
     * @var string
     */
    private $matchKey;

    /**
     * @var array | string[]
     */
    private $annotations = [];

    /**
     * @var string
     */
    private $visibility = 0;

    /**
     * @var int
     */
    private $modifiers = 0;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var Expression
     */
    private $expression;

    /**
     * PatternMethodBuilder constructor.
     * @param string $matchKey
     * @param Reader $reader
     * @param Expression $pattern
     */
    public function __construct(string $matchKey, Reader $reader, Expression $pattern)
    {
        $this->matchKey = $matchKey;
        $this->reader = $reader;
        $this->expression = $pattern;
    }

    public function visibility(...$params) : PatternBuilderMethod
    {
        $bitmask = array_reduce($params, function (int $carry, int $param) {
            return $carry | $param;
        }, 0);

        $this->visibility = $bitmask;

        return $this;
    }

    public function modifiers(...$params) : PatternBuilderMethod
    {
        $bitmask = array_reduce($params, function (int $carry, int $param) {
            return $carry | $param;
        }, 0);
        $this->modifiers = $bitmask;
        return $this;
    }

    /**
     * @param array $annotations
     * @return PatternBuilderMethod
     * @throws \ReflectionException
     */
    public function annotations(array $annotations) : PatternBuilderMethod
    {
        foreach ($annotations as $annotation) {
            $reflection = new \ReflectionClass($annotation);
            $this->confirmAnnotation($annotation, $reflection)->confirmTarget($annotation, $reflection);
            $this->annotations[] = $annotation;
        }

        return $this;
    }

    /**
     * @param string $annotation
     * @param bool $ignoreTarget
     * @return PatternBuilderMethod
     * @throws \ReflectionException
     */
    public function annotation(string $annotation, $ignoreTarget = false): PatternBuilderMethod
    {
        if (! $ignoreTarget) {
            return $this->annotations([$annotation]);
        }

        $reflection = new \ReflectionClass($annotation);
        $this->confirmAnnotation($annotation, $reflection);
        $this->annotations[] = $annotation;
        return $this;
    }

    public function expression(): PatternBuilder
    {
        return $this->and();
    }

    public function and(): PatternBuilder
    {
        $builder = PatternBuilder::copy($this->expression, $this->reader);
        $id = PatternId::next($this->matchKey);

        $method = new PatternMethod($id, $this->annotations, $this->visibility, $this->modifiers);
        return $builder->method($method);
    }

    private function confirmTarget(string $classfile, \ReflectionClass $reflection) : PatternBuilderMethod
    {
        /** @var Target $target */
        $target = $this->reader->getClassAnnotation($reflection, Target::class);
        if (is_null($target)) {
            $message = sprintf('%s is missing the @Target annotation', $classfile);
            throw new \InvalidArgumentException($message);
        }

        $isMethod = Target::TARGET_METHOD & $target->targets;
        if (0 === $isMethod) {
            $message = sprintf('%s is not a method  annotation', $classfile);
            throw new \InvalidArgumentException($message);
        }

        return $this;
    }

    /**
     * @param string $classfile
     * @param \ReflectionClass $reflection
     * @return PatternBuilderMethod
     */
    private function confirmAnnotation(string $classfile, \ReflectionClass $reflection): PatternBuilderMethod
    {
        $docComment = $reflection->getDocComment();

        if (false === strpos($docComment, '@Annotation')) {
            $message = sprintf('%s is missing the @Annotation annotation', $classfile);
            throw new \InvalidArgumentException($message);
        }

        return $this;
    }
}