<?php namespace Motorphp\SilexTools\ClassPattern\PatternConstant;
use Doctrine\Common\Annotations\Annotation;
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
     * @var Reader
     */
    private $reader;

    /**
     * @var string
     */
    private $visibility = 0;

    /**
     * PatternMethodBuilder constructor.
     * @param string $matchKey
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param array $annotations
     * @return Builder
     * @throws \ReflectionException
     */
    public function anyAnnotation(...$annotations) : Builder
    {
        foreach ($annotations as $annotation) {
            $this->confirmAnnotation($annotation);
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
            $this->confirmAnnotation($annotation);
        }

        $this->annotations = MatchPolicyAnnotations::matchAll($annotations);
        return $this;
    }

    /**
     * @param string $annotation
     * @return Builder
     * @throws \ReflectionException
     */
    public function annotation(string $annotation): Builder
    {
        return $this->allAnnotations($annotation);
    }

    public function visibility(...$params) : Builder
    {
        $bitmask = array_reduce($params, function (int $carry, int $param) {
            return $carry | $param;
        }, 0);
        $this->visibility = $bitmask;

        return $this;
    }

    public function build() : \Motorphp\SilexTools\ClassPattern\Pattern
    {
        $id = PatternId::next();
        return new Pattern($id, $this->annotations, $this->visibility);
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