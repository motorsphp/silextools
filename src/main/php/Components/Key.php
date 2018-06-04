<?php namespace Motorphp\SilexTools\Components;

interface Key
{
    function getId() : string;

    function write(SourceCodeWriter $writer);
}