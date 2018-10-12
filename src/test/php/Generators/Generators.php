<?php namespace Motorphp\SilexTools\Generators;

use Motorphp\SilexTools\Bootstrap\Signatures;
use Motorphp\SilexTools\NetteLibrary\BootstrapWritter;
use Motorphp\SilexTools\ParametersFile\ParametersFileWriter;
use PHPUnit\Framework\TestCase;
use Resource\Bootstrap\BootstrapInterface;
use Resource\Http\HealthCheckFactories;
use Resource\Providers\DummyProvider;

class GeneratorsTest extends TestCase
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

        $contents = Generators::default($scanSources, BootstrapInterface::class);
        static::assertNotEmpty($contents);
        die($contents);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGenerate()
    {
        $class = "";
        /**
         * Components\Components[]
         */
        $components = [];
        $signatures = new Signatures();

        $builder = new BootstrapWritter();
        $builder
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

    public function testParameters() : string
    {
        /** @var Components\Components $components */
        $components = null;
        $writer = new ParametersFileWriter();
        $components->visit($writer);

        return $writer->done();
    }
}
