<?php namespace Motorphp\SilexTools\Matcher;

use Motorphp\SilexAnnotations\Common\ContainerKey;
use Motorphp\SilexAnnotations\Common\ControllerFactory;
use Motorphp\SilexAnnotations\Common\ParamConverter;
use Motorphp\SilexAnnotations\Common\ServiceFactory;
use Motorphp\SilexAnnotations\Reader\ConstantsReader;
use Motorphp\SilexTools\Bootstrap\BootstrapBuilder;
use Motorphp\SilexTools\ClassPattern\Constants;
use Motorphp\SilexTools\ClassPattern\PatternBuilder;
use Motorphp\SilexTools\ClassScanner\ClassFile;
use Motorphp\SilexTools\ClassScanner\Scanner;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Resource\Http\HealthCheckFactories;
use Resource\Providers\DummyProvider;

use Swagger\Annotations\Delete;
use Swagger\Annotations\Get;
use Swagger\Annotations\Post;
use Swagger\Annotations\Put;

class SelectorMatcherTest extends TestCase
{
    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     */
    public function testEverything()
    {
        $scanner = new Scanner();

        $scanSources = [
            new \ReflectionClass(HealthCheckFactories::class),
            new \ReflectionClass(DummyProvider::class)
        ];
        $scanSources = array_map(function (\ReflectionClass $reflection) {
            return dirname($reflection->getFileName());
        }, $scanSources);
        $classFiles = $scanner->scanAll($scanSources);
        $classes = array_map(function (ClassFile $class) {
            return $class->getClassName();
        }, $classFiles);

        $reader = ConstantsReader::instance();
        $selector = SelectorBuilder::instance($reader)->addAndBuild(function (PatternBuilder $builder) {
            return $builder->setName(ContainerKey::class)
                ->constantPattern(ContainerKey::class)->annotation(ContainerKey::class)->visibility(Constants::VISIBILITY_ANY)
                ->and()
                    ->methodPattern(ServiceFactory::class)
                    ->annotation(ServiceFactory::class, true)
                    ->modifiers(Constants::MODIFIER_STATIC)
                ->and()
                    ->methodPattern('controller')->anyAnnotation(Get::class, Post::class, Put::class, Delete::class)
                ->and()
                    ->classPattern( ServiceProviderInterface::class)->implements(ServiceProviderInterface::class)
                ->and()
                    ->methodPattern(ControllerFactory::class)->annotation(ControllerFactory::class)
                ->and()
                    ->methodPattern(ParamConverter::class)->annotation(ParamConverter::class)
                ->expression()
                ;
            }
        );
        $matches = $selector->select($classes);

        $bootstrapBuilder = BootstrapBuilder::withContainerType($reader, Container::class);
        foreach ($matches->getMethods(ServiceFactory::class) as $reflection) {
            $bootstrapBuilder->addServiceFactory($reflection);
        }
        foreach ($matches->getConstants(ContainerKey::class) as $reflection) {
            $bootstrapBuilder->addFactoryKey($reflection);
        }
        foreach ($matches->getClasses(ServiceProviderInterface::class) as $reflection) {
            $bootstrapBuilder->addProvider($reflection);
        }
        foreach ($matches->getMethods('controller') as $reflection) {
            $bootstrapBuilder->addController($reflection);
        }

        $x = $bootstrapBuilder->build();
        die($x);

        $methods = $matches->getMethods('controller');
        var_dump($methods);

        $method = $methods[0];
        die($method->getReturnType()->getName());

        $methods = $matches->getMethods(ControllerFactory::class);
        var_dump($methods);

        die('ss');
    }
}