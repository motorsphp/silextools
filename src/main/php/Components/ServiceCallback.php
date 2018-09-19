<?php namespace Motorphp\SilexTools\Components;

/**
 * Represents the source code declaration of a bootstrap component
 */
interface ServiceCallback
{
    function writeKey(SourceCodeWriter $writer) : Value;

    function writeMethod(SourceCodeWriter $writer) : Value;
}