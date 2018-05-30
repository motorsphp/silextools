<?php namespace Motorphp\SilexTools\Components;

interface Component
{
    function acceptVisit(ComponentsVisitor $from);
}