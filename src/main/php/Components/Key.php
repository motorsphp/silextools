<?php namespace Motorphp\SilexTools\Components;

interface Key
{
    function getId();

    function write(SourceCodeWriter $writer);
}