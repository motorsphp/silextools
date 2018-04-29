<?php namespace Motorphp\SilexTools\Bootstrap;

use Motorphp\SilexTools\NetteLibrary\MethodBodyPart;

interface Declaration
{
    function canBuild(): bool;

    function build(): MethodBodyPart;

}