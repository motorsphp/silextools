<?php namespace Motorphp\SilexTools\Generators;

use Motorphp\SilexAnnotations\Common\ContainerKey;
use Motorphp\SilexAnnotations\Common\ControllerFactory;
use Motorphp\SilexAnnotations\Common\ParamConverter;
use Motorphp\SilexAnnotations\Common\ServiceFactory;
use Motorphp\SilexAnnotations\Reader\ConstantsReader;
use Motorphp\SilexTools\Bootstrap\Builders;
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

class Generators
{
    /**
     * @param array|string[] $sources list of directories to scan
     * @param string $class prototype for the bootstrap class
     * @return string
     * @throws \ReflectionException
     */
    public static function default(array $sources, string $class) : string
    {
        /**
         * @param PatternBuilder $builder
         * @return mixed
         * @return PatternBuilder
         * @throws \ReflectionException
         */
        $patternBuilder = function (PatternBuilder $builder) {
            return $builder->setName(ContainerKey::class)
                ->constantPattern(ContainerKey::class)
                    ->annotation(ContainerKey::class)->visibility(Constants::VISIBILITY_ANY)
                ->andMethod(ServiceFactory::class)
                    ->annotation(ServiceFactory::class, true)->modifiers(Constants::MODIFIER_STATIC)
                ->andMethod('controller')
                    ->anyAnnotation(Get::class, Post::class, Put::class, Delete::class)
                ->andClass(ServiceProviderInterface::class)
                    ->implements(ServiceProviderInterface::class)
                ->andMethod(ControllerFactory::class)
                    ->annotation(ControllerFactory::class)
                ->andMethod(ParamConverter::class)
                    ->annotation(ParamConverter::class)
                ->expression()
                ;
        };

        $reader = ConstantsReader::instance();

        $matches = Matches::scanAndSelect($sources, new Scanner(), SelectorBuilder::instance($reader)->addAndBuild($patternBuilder));
        $contents = Builders::configureFactories($class, $reader)
            ->addAllServiceFactories(
                $matches->getMethods(ServiceFactory::class)
            )
            ->addAllFactoryKeys(
                $matches->getConstants(ContainerKey::class)
            )
            ->configureProviders($class)
            ->addAllProviders(
                $matches->getClasses(ServiceProviderInterface::class)
            )
            ->configureHttp($class)
            ->addAllControllers(
                $matches->getMethods('controller')
            )
            ->addAllConverters(
                $matches->getMethods(ParamConverter::class)
            )
            ->buildClass()->sameAs($class)
            ->build()
        ;

        return $contents;
    }
}
