<?php namespace Motorphp\SilexTools\Components\Provider;

use Motorphp\SilexTools\Components\Component;
use Motorphp\SilexTools\Components\ComponentsVisitor;
use Motorphp\SilexTools\Components\Provider;

class ComponentAdapter implements Component
{
    /** @var Provider */
    private $provider;

    /**
     * ComponentAdapter constructor.
     * @param Provider $provider
     */
    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    function acceptVisit(ComponentsVisitor $from)
    {
        $from->visitProvider($this->provider);
    }
}