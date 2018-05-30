<?php namespace Motorphp\SilexTools\Components;

use Motorphp\SilexTools\Components\Component;

class Factory implements Component
{
    /**
     * @var Key
     */
    private $key;

    /**
     * @var \ReflectionMethod
     */
    private $reflection;

    public function __construct(Key $key, \ReflectionMethod $reflection)
    {
        $this->key = $key;
        $this->reflection = $reflection;
    }

    function writeKey(SourceCodeWriter $writer)
    {
        $this->key->write($writer);
    }

    function writeCallback(SourceCodeWriter $writer)
    {
        $writer->writeStaticInvocation($this->reflection);
    }

    function acceptVisit(ComponentsVisitor $from)
    {
        $from->visitFactory($this);
    }

}