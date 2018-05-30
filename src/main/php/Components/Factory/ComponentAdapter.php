<?php namespace Motorphp\SilexTools\Components\Factory;

use Motorphp\SilexTools\Components\Component;
use Motorphp\SilexTools\Components\ComponentsVisitor;
use Motorphp\SilexTools\Components\Factory;

class ComponentAdapter implements Component
{
    /** @var Factory */
    private $factory;

    /**
     * ComponentAdapter constructor.
     * @param Factory $factory
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    function acceptVisit(ComponentsVisitor $from)
    {
        return $from->visitFactory($this->factory);
    }
}