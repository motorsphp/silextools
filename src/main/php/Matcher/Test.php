<?php namespace Motorphp\SilexTools\Matcher;

interface Test
{
    function test(\Reflector $reflector) : bool;
}