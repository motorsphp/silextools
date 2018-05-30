<?php namespace Motorphp\SilexTools\Components;

class Components
{
    /** @var array | Component[] */
    private $list;

    /**
     * Components constructor.
     * @param array|Component[] $list
     */
    public function __construct($list)
    {
        $this->list = $list;
    }

    function visit(ComponentsVisitor $visitor)
    {
        foreach ($this->list as $component) {
            $component->acceptVisit($visitor);
        }
    }
}