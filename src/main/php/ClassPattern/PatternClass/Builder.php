<?php namespace Motorphp\SilexTools\ClassPattern\PatternClass;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\Reader;
use Motorphp\SilexTools\ClassPattern\PatternBuilder;
use Motorphp\SilexTools\ClassPattern\PatternId;

class Builder extends PatternBuilder
{
    /**
     * @var Reader
     */
    private $reader;

    private $interfaces = [];

    private $parents = [];

    private $methodAnnotations = [];

    private $classAnnotations = [];

    /**
     * PatternClassBuilder constructor.
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param string $interface
     * @return Builder
     * @throws \ReflectionException
     */
    public function implements(string $interface) : Builder
    {
        return $this->implementsAll([$interface]);
    }

    /**
     * @param array $interfaces
     * @return Builder
     * @throws \ReflectionException
     */
    public function implementsAll(array $interfaces) : Builder
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

    /**
     * @param string $clazz
     * @return Builder
     * @throws \ReflectionException
     */
    public function annotation(string $clazz) : Builder
    {
        return $this->annotations([$clazz]);
    }

    /**
     * @param array $annotations
     * @return Builder
     * @throws \ReflectionException
     */
    public function annotations(array $annotations) : Builder
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

    public function build() : \Motorphp\SilexTools\ClassPattern\Pattern
    {
        $id = PatternId::next();
        return new Pattern(
            $id,
            $this->interfaces,
            $this->parents,
            $this->methodAnnotations,
            $this->classAnnotations
        );
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