<?php namespace Motorphp\SilexTools\ClassPattern;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\Reader;

class PatternBuilderConstant
{
    /**
     * @var string
     */
    private $matchKey;

    /**
     * @var MatchPolicyAnnotations
     */
    private $annotations = null;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var Expression
     */
    private $expression;

    /**
     * @var string
     */
    private $visibility = 0;

    /**
     * PatternMethodBuilder constructor.
     * @param string $matchKey
     * @param Reader $reader
     * @param Expression $expression
     */
    public function __construct(string $matchKey, Reader $reader, Expression $expression)
    {
        $this->reader = $reader;
        $this->expression = $expression;
        $this->matchKey = $matchKey;
    }

    /**
     * @param array $annotations
     * @return PatternBuilderConstant
     * @throws \ReflectionException
     */
    public function anyAnnotation(...$annotations) : PatternBuilderConstant
    {
        foreach ($annotations as $annotation) {
            $this->confirmAnnotation($annotation);
        }

        $this->annotations = MatchPolicyAnnotations::matchAny($annotations);
        return $this;
    }

    /**
     * @param array $annotations
     * @return PatternBuilderConstant
     * @throws \ReflectionException
     */
    public function allAnnotations(...$annotations) : PatternBuilderConstant
    {
        foreach ($annotations as $annotation) {
            $this->confirmAnnotation($annotation);
        }

        $this->annotations = MatchPolicyAnnotations::matchAll($annotations);
        return $this;
    }


    /**
     * @param string $annotation
     * @return PatternBuilderConstant
     * @throws \ReflectionException
     */
    public function annotation(string $annotation): PatternBuilderConstant
    {
        return $this->allAnnotations($annotation);
    }

    public function visibility(...$params) : PatternBuilderConstant
    {
        $bitmask = array_reduce($params, function (int $carry, int $param) {
            return $carry | $param;
        }, 0);
        $this->visibility = $bitmask;

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
        $constant = new PatternConstant($id, $this->annotations, $this->visibility);
        return $builder->constant($constant);
    }

    /**
     * @param string $classfile
     * @return bool
     * @throws \ReflectionException
     * @throws \InvalidArgumentException
     */
    private function confirmAnnotation(string $classfile): bool
    {
        $reflection = new \ReflectionClass($classfile);
        $docComment = $reflection->getDocComment();

        if (false === strpos($docComment, '@Annotation')) {
            $message = sprintf('%s is missing the @Annotation annotation', $classfile);
            throw new \InvalidArgumentException($message);
        }

        /** @var Target $target */
        $target = $this->reader->getClassAnnotation($reflection, Target::class);
        if (is_null($target)) {
            $message = sprintf('%s is missing the @Target annotation', $classfile);
            throw new \InvalidArgumentException($message);
        }

        $isMethod = Target::TARGET_PROPERTY & $target->targets;
        if (0 === $isMethod) {
            $message = sprintf('%s is not a method  annotation', $classfile);
            throw new \InvalidArgumentException($message);
        }

        return true;
    }
}