<?php namespace Motorphp\SilexTools\Generators;

use Motorphp\SilexAnnotations\Common\ContainerKey;
use Motorphp\SilexAnnotations\Common\ControllerFactory;
use Motorphp\SilexAnnotations\Common\ParamConverter;
use Motorphp\SilexAnnotations\Common\Parameter;
use Motorphp\SilexAnnotations\Common\ServiceFactory;
use Motorphp\SilexAnnotations\Reader\ConstantsReader;
use Motorphp\SilexTools\ClassPattern\Matches\Handler;
use Motorphp\SilexTools\Components;
use Motorphp\SilexTools\ClassPattern\Constants;
use Motorphp\SilexTools\ClassPattern\PatternBuilder;
use Motorphp\SilexTools\ClassScanner\Scanner;
use Motorphp\SilexTools\Matcher\Matches;
use Motorphp\SilexTools\Matcher\SelectorBuilder;
use Pimple\ServiceProviderInterface;
use Swagger\Annotations\Delete;
use Swagger\Annotations\Get;
use Swagger\Annotations\Post;
use Swagger\Annotations\Put;

class SourceCodeReader
{
    /** @var ConstantsReader */
    private $reader;

    public function __construct(ConstantsReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param PatternBuilder $builder
     * @return PatternBuilder
     * @throws \ReflectionException
     */
    private function buildPattern(PatternBuilder $builder) : PatternBuilder
    {
        return $builder->setName(ContainerKey::class)
            ->constantPattern(ContainerKey::class)
                ->annotation(ContainerKey::class)
                ->visibility(Constants::VISIBILITY_ANY)
            ->andMethod(ServiceFactory::class)
                ->annotation(ServiceFactory::class, true)
                ->modifiers(Constants::MODIFIER_STATIC)
            ->andMethod('controller')
                ->anyAnnotation(Get::class, Post::class, Put::class, Delete::class)
            ->andClass(ServiceProviderInterface::class)
                ->implements(ServiceProviderInterface::class)
            ->andMethod(ControllerFactory::class)
                ->annotation(ControllerFactory::class)
            ->andMethod(ParamConverter::class)
                ->annotation(ParamConverter::class)
            ->andMethod(Parameter::class)
                ->annotation(Parameter::class)
            ->expression()
            ;
    }

    /**
     * @param array | string[] $sources
     * @return Components\Components
     */
    public function scan(array $sources) : Components\Components
    {
        $matches = Matches::scanAndSelect(
            $sources,
            new Scanner(),
            SelectorBuilder::instance($this->reader)
                ->addAndBuild(function (PatternBuilder $builder) {
                    return $this->buildPattern($builder);
                })
        );

        $components = Components\Components\Builder::instance();
        $callbacks = new BuilderCallbacks($components);

        $handler = new Handler();
        $handler
            ->invoke($callbacks->addFactory($this->reader))
                ->when(\ReflectionMethod::class, ServiceFactory::class)
            ->invoke($callbacks->addController($this->reader))
                ->when(\ReflectionMethod::class, 'controller')
            ->invoke($callbacks->addConverter($this->reader))
                ->when(\ReflectionMethod::class, ParamConverter::class)
            ->invoke($callbacks->addParameter($this->reader))
                ->when(\ReflectionMethod::class, Parameter::class)
                ->done()
            ->forEach(ContainerKey::class,\ReflectionClassConstant::class)
                ->withCallback($callbacks->addKey($this->reader))
            ->forEach(ServiceProviderInterface::class,\ReflectionClass::class)
                ->withCallback($callbacks->addProvider($this->reader))
            ->handle($matches)
        ;

        return $components->build();
    }
}