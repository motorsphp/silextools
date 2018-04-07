<?php namespace Motorphp\SilexTools\ClassPattern;

interface MatchesCollector
{
    public function addMatchMethod(\ReflectionMethod $reflection, $key);

    public function addMatchClass(\ReflectionClass $reflection, $key);

    public function addMatchParam(\ReflectionParameter $reflection, $key);

    public function addMatchConstant(\ReflectionClassConstant $reflection, $key);
}