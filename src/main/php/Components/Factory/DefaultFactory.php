<?php namespace Motorphp\SilexTools\Components\Factory;

use Motorphp\SilexTools\Components\Factory;
use Motorphp\SilexTools\Components\Key;
use Motorphp\SilexTools\Components\SourceCodeWriter;

class DefaultFactory implements Factory
{
    /**
     * @var Key
     */
    private $key;

    /**
     * @var \ReflectionMethod
     */
    private $reflection;

    /** @var Placement */
    private $placement;

    /** @var Capabilities */
    private $capabilities;

    public function __construct(Key $key, \ReflectionMethod $reflection, Placement $placement, Capabilities $capabilities)
    {
        $this->key = $key;
        $this->reflection = $reflection;
        $this->placement = $placement;
        $this->capabilities = $capabilities;
    }

    function writeKey(SourceCodeWriter $writer)
    {
        $this->key->write($writer);
    }

    function writeCallback(SourceCodeWriter $writer)
    {
        $writer->writeStaticInvocation($this->reflection);
    }

    /**
     * @return Placement
     */
    public function getPlacement(): Placement
    {
        return $this->placement;
    }

    public function getCapabilities(): Capabilities
    {
        return $this->capabilities;
    }
}