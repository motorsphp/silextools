<?php namespace Motorphp\SilexTools\Bootstrap;

interface Declaration
{
    function canBuild(): bool;

    function build(BootstrapMethodBuilder $builder): BootstrapMethodBuilder;
}