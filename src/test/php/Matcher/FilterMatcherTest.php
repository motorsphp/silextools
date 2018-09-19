<?php namespace Motorphp\SilexTools\Matcher;

use Motorphp\SilexAnnotations\Common\ContainerKey;
use Motorphp\SilexTools\ClassPattern\PatternGroupBuilder;
use Motorphp\SilexTools\ClassScanner\ClassFile;
use Motorphp\SilexTools\ClassScanner\Scanner;
use PHPUnit\Framework\TestCase;
use Resource\Http\HealthCheckFactories;
use Swagger\Annotations\Get;

class FilterMatcherTest  extends TestCase
{
    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     */
    public function xtestMatcher()
    {
        $scanner = new Scanner();

        $c = new \ReflectionClass(HealthCheckFactories::class);
        $classFiles = $scanner->scan(dirname($c->getFileName()));

//        $matcher = MatchExpressionBuilder::all(function (PatternBuilder $builder) {
//            return $builder->setName(ServiceFactory::class)
//                ->constantPattern()->annotation(ContainerKey::class)
//                ->expression();
//            })->build()
//        ;


        $matcher = FilterBuilder::any(
            function (PatternGroupBuilder $builder) {
                return $builder->setName(ContainerKey::class)
                    ->constantPattern()->annotation(ContainerKey::class)
                    ->expression()
                    ;
            }
        )
            ->add(
                function (PatternGroupBuilder $builder) {
                    return $builder->setName(Get::class)
                        ->methodPattern()->annotation(Get::class, true)
                        ->expression()
                        ;
                }
            )
            ->build()
        ;


        $matchedClassFiles = array_filter($classFiles, function (ClassFile $file) use ($matcher) {
            $class = $file->getClassName();
            return $matcher->isMatching($class);
        });

        $matched = array_map(function (ClassFile $class) {
            return $class->getClassName();
        }, $matchedClassFiles);




        echo implode(PHP_EOL, $matched) . PHP_EOL;
        die('ss');
    }
}