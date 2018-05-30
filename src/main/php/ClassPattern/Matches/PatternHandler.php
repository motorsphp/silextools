<?php namespace Motorphp\SilexTools\ClassPattern\Matches;

interface PatternHandler
{
    function getPatternId() : string;

    function handle(PatternMatches $matches);
}