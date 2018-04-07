<?php namespace Motorphp\SilexTools\ClassPattern;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\Reader;

class PatternBuilderClass
{
    /**
     * @var string
     */
    private $matchKey;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var Expression
     */
    private $pattern;

    private $interfaces = [];

    private $parents = [];

    private $methodAnnotations = [];

    private $classAnnotations = [];

    /**
     * PatternClassBuilder constructor.
     * @param string $matchKey
     * @param Reader $reader
     * @param Expression $pattern
     */
    public function __construct(string $matchKey, Reader $reader, Expression $pattern)
    {
        $this->reader = $reader;
        $this->pattern = $pattern;
        $this->matchKey = $matchKey;
    }

    public function expression(): PatternBuilder
    {
        return $this->and();
    }

    public function and() : PatternBuilder
    {
        $builder = PatternBuilder::copy($this->pattern, $this->reader);
        $id = PatternId::next($this->matchKey);

        $pattern = new PatternClass(
            $id,
            $this->interfaces,
            $this->parents,
            $this->methodAnnotations,
            $this->classAnnotations
        );
        return $builder->class($pattern);
    }

    /**
     * @param string $interface
     * @return PatternBuilderClass
     * @throws \ReflectionException
     */
    public function implements(string $interface) : PatternBuilderClass
    {
        return $this->implementsAll([$interface]);
    }

    /**
     * @param array $interfaces
     * @return PatternBuilderClass
     * @throws \ReflectionException
     */
    public function implementsAll(array $interfaces) : PatternBuilderClass
    {
        foreach ($interfaces as $interface) {
            $reflection = new \ReflectionClass($interface);
            if ($reflection->isInterface()) {
                $this->interfaces[] = $interface;
            } else {
                $msg = sprintf('%s is not an interface');
                throw new \InvalidArgumentException($msg);
            }
        }
        return $this;
    }

    public function annotation(string $clazz) : PatternBuilderClass
    {
        return $this->annotations([$clazz]);
    }

    public function annotations(array $annotations) : PatternBuilderClass
    {
        foreach ($annotations as $interface) {
            $target = $this->confirmAnnotation($interface);

            if (Target::TARGET_METHOD === $target->targets) {
                $this->methodAnnotations[] = $interface;
            } else if (Target::TARGET_CLASS === $target->targets) {
                $this->classAnnotations[] = $interface;
            } else {
                $msg = sprintf('%s is not an interface');
                throw new \InvalidArgumentException($msg);
            }

        }
    }

    /**
     * @param string $classfile
     * @return Target
     * @throws \ReflectionException
     */
    private function confirmAnnotation(string $classfile): Target
    {
        $reflection = new \ReflectionClass($classfile);

        /** @var Annotation $annotationObject */
        $annotationObject = $this->reader->getClassAnnotation($reflection, Annotation::class);
        /** @var Target $target */
        $target = $this->reader->getClassAnnotation($reflection, Target::class);

        if (is_null($annotationObject)) {
            $message = sprintf('%s is missing the @Annotation annotation', $classfile);
            throw new \InvalidArgumentException($message);
        }

        if (is_null($target)) {
            $message = sprintf('%s is missing the @Target annotation', $classfile);
            throw new \InvalidArgumentException($message);
        }

        return $target;
    }
}