<?php namespace Motorphp\SilexTools\ClassPattern\PatternMethod;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\Reader;
use Motorphp\SilexTools\ClassPattern\MatchPolicyAnnotations;
use Motorphp\SilexTools\ClassPattern\PatternBuilder;
use Motorphp\SilexTools\ClassPattern\PatternId;

class Builder extends PatternBuilder
{
    /**
     * @var MatchPolicyAnnotations
     */
    private $annotations = null;

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
     * PatternMethodBuilder constructor.
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function visibility(...$params) : Builder
    {
        $bitmask = array_reduce($params, function (int $carry, int $param) {
            return $carry | $param;
        }, 0);

        $this->visibility = $bitmask;

        return $this;
    }

    public function modifiers(...$params) : Builder
    {
        $bitmask = array_reduce($params, function (int $carry, int $param) {
            return $carry | $param;
        }, 0);
        $this->modifiers = $bitmask;
        return $this;
    }

    /**
     * @param array $annotations
     * @return Builder
     * @throws \ReflectionException
     */
    public function anyAnnotation(...$annotations) : Builder
    {
        foreach ($annotations as $annotation) {
            $reflection = new \ReflectionClass($annotation);
//            $this->confirmAnnotation($annotation, $reflection)->confirmTarget($annotation, $reflection);
            $this->confirmAnnotation($annotation, $reflection);
        }

        $this->annotations = MatchPolicyAnnotations::matchAny($annotations);
        return $this;
    }

    /**
     * @param array $annotations
     * @return Builder
     * @throws \ReflectionException
     */
    public function allAnnotations(...$annotations) : Builder
    {
        foreach ($annotations as $annotation) {
            $reflection = new \ReflectionClass($annotation);
//            $this->confirmAnnotation($annotation, $reflection)->confirmTarget($annotation, $reflection);
            $this->confirmAnnotation($annotation, $reflection);
        }

        $this->annotations = MatchPolicyAnnotations::matchAll($annotations);
        return $this;
    }

    /**
     * @param string $annotation
     * @param bool $ignoreTarget enable or disable check for the annotations target
     * @return Builder
     * @throws \ReflectionException
     */
    public function annotation(string $annotation, $ignoreTarget = false): Builder
    {
        if (! $ignoreTarget) {
            return $this->allAnnotations($annotation);
        }

        $reflection = new \ReflectionClass($annotation);
        $this->confirmAnnotation($annotation, $reflection);
        $this->annotations =  MatchPolicyAnnotations::matchAll([$annotation]);
        return $this;
    }

    public function build() : \Motorphp\SilexTools\ClassPattern\Pattern
    {
        $id = PatternId::next();
        return new Pattern($id, $this->annotations, $this->visibility, $this->modifiers);
    }

    private function confirmTarget(string $classfile, \ReflectionClass $reflection) : Builder
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
     * @return Builder
     */
    private function confirmAnnotation(string $classfile, \ReflectionClass $reflection): Builder
    {
        $docComment = $reflection->getDocComment();

        if (false === strpos($docComment, '@Annotation')) {
            $message = sprintf('%s is missing the @Annotation annotation', $classfile);
            throw new \InvalidArgumentException($message);
        }

        return $this;
    }
}