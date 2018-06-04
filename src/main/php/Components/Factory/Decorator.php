<?php namespace Motorphp\SilexTools\Components\Factory;

use Motorphp\SilexTools\Components\Factory;
use Motorphp\SilexTools\Components\SourceCodeWriter;

class Decorator
{
    /** @var Factory */
    private $factory;

    /**
     * Decorator constructor.
     * @param Factory $factory
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    function writeKey(SourceCodeWriter $writer)
    {
        $this->factory->writeKey($writer);
    }

    function writeCallback(SourceCodeWriter $writer)
    {
        $this->factory->writeCallback($writer);
    }

    /**
     * @return Placement
     */
    public function getPlacement(): Placement
    {
        return $this->factory->getPlacement();
    }
}