<?php namespace Motorphp\SilexTools\Generators;

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
}
