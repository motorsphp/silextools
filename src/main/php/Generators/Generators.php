<?php namespace Motorphp\SilexTools\Generators;

use Motorphp\SilexAnnotations\Reader\ConstantsReader;
use Motorphp\SilexTools\Bootstrap\BootstrapBuilder;
use Motorphp\SilexTools\Bootstrap\Signatures;
use Motorphp\SilexTools\NetteLibrary\BootstrapBuilderAdapter;
use Motorphp\SilexTools\Components;
use Motorphp\SilexTools\Bootstrap\Factories;
use Motorphp\SilexTools\Bootstrap\Routes;
use Motorphp\SilexTools\Bootstrap\Providers;

class Generators
{
    public static function parameters(Components\Components $components) : string
    {
        $string = '';
        foreach ($components->getParameters() as $parameter) {

            $string .= sprintf("%s=%s", $parameter->getName(), $parameter->getDefault());
            $string .= "\n";
        }

        return $string;
    }

    /**
     * @param array|Components\Component[] $components
     * @param string $class
     * @return string
     * @throws \ReflectionException
     */
    public static function bootstrap(Components\Components $components, string $class) : string
    {
        $signatures = new Signatures();

        $builder = new BootstrapBuilderAdapter();
        return $builder
            ->withClassname($class)
            ->withSameNamespaceAsClass($class)
            ->withProviders($signatures->configureProviders($class))
                ->withComponents($components)
                ->done()
            ->withRoutes($signatures->configureHttp($class))
                ->withComponents($components)
                ->done()
            ->withFactories($signatures->configureFactories($class))
                ->withComponents($components)
                ->done()
            ->build()
        ;
    }

    /**
     * @param array|string[] $sources
     * @return Components\Components
     */
    public static function components(array $sources) : Components\Components
    {
        $reader = new SourceCodeReader(ConstantsReader::instance());
        return $reader->scan($sources);
    }
}
