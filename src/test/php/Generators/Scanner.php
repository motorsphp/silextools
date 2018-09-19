<?php namespace Motorphp\SilexTools\Generators;

use PHPUnit\Framework\TestCase;
use Motorphp\SilexAnnotations\Common\ContainerKey;
use Motorphp\SilexAnnotations\Common\ParamConverter;
use Motorphp\SilexAnnotations\Common\Parameter;
use Motorphp\SilexAnnotations\Common\ServiceFactory;
use Motorphp\SilexAnnotations\Reader\ConstantsReader;
use Motorphp\SilexTools\Annotations;
use Motorphp\SilexTools\ClassPattern\PatternMatches;
use Motorphp\SilexTools\ClassPattern\PatternClass;
use Motorphp\SilexTools\ClassPattern\PatternConstant;
use Motorphp\SilexTools\ClassPattern\PatternMethod;
use Motorphp\SilexTools\Components;
use Motorphp\SilexTools\ClassPattern\Constants;
use Pimple\ServiceProviderInterface;

use Swagger\Annotations\Delete;
use Swagger\Annotations\Get;
use Swagger\Annotations\Post;
use Swagger\Annotations\Put;
class Scanner extends TestCase
{
    /**
     * @param array | string[] $sources
     * @return Components\Components
     */
    public function scan(array $sources) : Components\Components
    {
        $this->reader = ConstantsReader::instance();

        $components = Annotations\BindingsBuilders::instance();
        $reader = $this->reader;

        $transformer = new Transformer($this->reader);
        $transformer // container keys
        ->constantPattern(function (PatternConstant\Builder $builder) {
            $builder
                ->annotation(ContainerKey::class)
                ->visibility(Constants::VISIBILITY_ANY)
            ;
        })
            ->transform(function (PatternMatches $matches) use ($components, $reader) {
                $reflectors = $matches->constants();
                $components->addAllKeys($reflectors , $reader);
            })

            ->and($transformer) // service factories keys
            ->methodPattern(function (PatternMethod\Builder $builder) {
                $builder
                    ->annotation(ServiceFactory::class, true)
                    ->modifiers(Constants::MODIFIER_STATIC)
                ;
            })
            ->transform(function (PatternMatches $matches) use ($components, $reader) {
                $reflectors = $matches->methods();
                $components->addAllFactories($reflectors , $reader);
            })

            ->and($transformer) // controllers
            ->methodPattern(function (PatternMethod\Builder $builder) {
                $builder->anyAnnotation(Get::class, Post::class, Put::class, Delete::class);
            })
            ->transform(function (PatternMatches $matches) use ($components, $reader) {
                $reflectors = $matches->methods();
                $components->addAllControllers($reflectors , $reader);
            })

            ->and($transformer) // service providers
            ->classPattern(function (PatternClass\Builder $builder) {
                $builder->implements(ServiceProviderInterface::class);
            })
            ->transform(function (PatternMatches $matches) use ($components, $reader) {
                $reflectors = $matches->methods();
                $components->addAllProviders($reflectors , $reader);
            })

            ->and($transformer) // param converters
            ->methodPattern(function (PatternMethod\Builder $builder) {
                $builder->annotation(ParamConverter::class);
            })
            ->transform(function (PatternMatches $matches) use ($components, $reader) {
                $reflectors = $matches->methods();
                $components->addAllConverters($reflectors , $reader);
            })

            ->and($transformer) // parameters converters
            ->methodPattern(function (PatternMethod\Builder $builder) {
                $builder->annotation(Parameter::class);
            })
            ->transform(function (PatternMatches $matches) use ($components, $reader) {
                $reflectors = $matches->methods();
                $components->addAllParameters($reflectors , $reader);
            })
            ->done($transformer)
            ->run($sources, new Scanner())
        ;

        return $components->build();
    }
}