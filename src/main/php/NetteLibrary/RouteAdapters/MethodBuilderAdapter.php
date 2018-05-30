<?php namespace Motorphp\SilexTools\NetteLibrary\RouteAdapters;

use Motorphp\SilexTools\Bootstrap\BootstrapBuilder;
use Motorphp\SilexTools\Bootstrap\MethodBuilder;
use Motorphp\SilexTools\Components\Components;
use Motorphp\SilexTools\NetteLibrary\BootstrapBuilderAdapter;
use Motorphp\SilexTools\NetteLibrary\Method\AbstractBuilder;

class MethodBuilderAdapter extends AbstractBuilder implements MethodBuilder
{
    /**
     * @var BootstrapBuilderAdapter
     */
    private $parent;

    public function __construct(BootstrapBuilderAdapter $parent)
    {
        $this->parent = $parent;
    }

    function withComponents(Components $components): MethodBuilder
    {
        $writer = new MethodBodyWriter();
        $components->visit($writer);

        $methodBody = $writer->getMethodBody();
        parent::setMethodBody($methodBody);

        return $this;
    }

    function done(): BootstrapBuilder
    {
        return parent::configure($this->parent);
    }
}