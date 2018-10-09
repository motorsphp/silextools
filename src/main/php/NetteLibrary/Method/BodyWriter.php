<?php namespace Motorphp\SilexTools\NetteLibrary\Method;

use Motorphp\SilexTools\Components\ComponentsVisitor;

interface BodyWriter extends ComponentsVisitor
{
    function getMethodBody() : MethodBody;
}