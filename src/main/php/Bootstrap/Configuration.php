<?php namespace Motorphp\SilexTools\Bootstrap;

use Motorphp\SilexTools\NetteLibrary\MethodBodyPart;

interface Configuration
{
    function canBuild(): bool;

    function build(): MethodBodyPart;

}