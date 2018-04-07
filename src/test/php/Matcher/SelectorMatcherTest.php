<?php namespace Motorphp\SilexTools\Matcher;

use Motorphp\SilexAnnotations\Common\ContainerKey;
use Motorphp\SilexAnnotations\Common\ControllerFactory;
use Motorphp\SilexAnnotations\Common\ParamConverter;
use Motorphp\SilexAnnotations\Common\ServiceFactory;
use Motorphp\SilexTools\ClassPattern\Constants;
use Motorphp\SilexTools\ClassPattern\PatternBuilder;
use Motorphp\SilexTools\ClassScanner\ClassFile;
use Motorphp\SilexTools\ClassScanner\Scanner;
use PHPUnit\Framework\TestCase;
use Pimple\ServiceProviderInterface;
use Resource\Http\HealthCheckFactories;
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

        $c = new \ReflectionClass(HealthCheckFactories::class);
        $classFiles = $scanner->scan(dirname($c->getFileName()));


        $selector = SelectorBuilder::selector(function (PatternBuilder $builder) {
            return $builder->setName(ContainerKey::class)
                ->constantPattern(ContainerKey::class)->annotation(ContainerKey::class)
                ->and()
                ->methodPattern(ServiceFactory::class)
                ->annotation(ServiceFactory::class, true)
                ->modifiers(Constants::MODIFIER_STATIC)
                ->and()
                ->methodPattern('controller')->annotation(Get::class, true)
                ->and()
                ->methodPattern('controller')->annotation(Post::class, true)
                ->and()
                ->methodPattern('controller')->annotation(Put::class, true)
                ->and()
                ->methodPattern('controller')->annotation(Delete::class, true)
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

        $classes = array_map(function (ClassFile $class) {
            return $class->getClassName();
        }, $classFiles);

        $matches = $selector->select($classes);


        $methods = $matches->getMethods('controller');
        var_dump($methods);

        $method = $methods[0];
        die($method->getReturnType()->getName());

        $methods = $matches->getMethods(ControllerFactory::class);
        var_dump($methods);

        die('ss');
    }
}