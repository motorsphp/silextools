<?php namespace Motorphp\SilexTools\Matcher;

use Motorphp\SilexAnnotations\Common\ContainerKey;
use Motorphp\SilexAnnotations\Common\ControllerFactory;
use Motorphp\SilexAnnotations\Common\ParamConverter;
use Motorphp\SilexAnnotations\Common\ServiceFactory;
use Motorphp\SilexAnnotations\Reader\ConstantsReader;
use Motorphp\SilexTools\Bootstrap\Builders;
use Motorphp\SilexTools\ClassPattern\Constants;
use Motorphp\SilexTools\ClassPattern\PatternBuilder;
use Motorphp\SilexTools\ClassScanner\ClassFile;
use Motorphp\SilexTools\ClassScanner\Scanner;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Resource\Bootstrap\BootstrapInterface;
use Resource\Http\HealthCheckFactories;
use Resource\Providers\DummyProvider;

use Swagger\Annotations\Delete;
use Swagger\Annotations\Get;
use Swagger\Annotations\Post;
use Swagger\Annotations\Put;

class SelectorMatcherTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testEverything()
    {
        $scanSources = [
            dirname((new \ReflectionClass(HealthCheckFactories::class))->getFileName()),
            dirname((new \ReflectionClass(DummyProvider::class))->getFileName())
        ];

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

        $matches = Matches::scanAndSelect($scanSources, new Scanner(), SelectorBuilder::instance($reader)->addAndBuild($patternBuilder));
        $contents = Builders::configureFactories(BootstrapInterface::class, $reader)
            ->addAllServiceFactories(
                $matches->getMethods(ServiceFactory::class)
            )
            ->addAllFactoryKeys(
                $matches->getConstants(ContainerKey::class)
            )
            ->configureProviders(BootstrapInterface::class)
                ->addAllProviders(
                    $matches->getClasses(ServiceProviderInterface::class)
                )
            ->configureHttp(BootstrapInterface::class)
                ->addAllControllers(
                    $matches->getMethods('controller')
                )
            ->buildClass()
                ->build()
            ;

        static::assertNotEmpty($contents);
        die($contents);
    }
}