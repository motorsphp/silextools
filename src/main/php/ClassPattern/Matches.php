<?php namespace Motorphp\SilexTools\ClassPattern;

interface Matches
{
    /**
     * @param string $matchKey
     * @return array|\ReflectionParameter[]|null
     */
    public function getParams(string $matchKey) : ?array;

    /**
     * @param string $matchKey
     * @return array|\ReflectionClassConstant[]|null
     */
    public function getConstants(string $matchKey) : ?array;

    /**
     * @param string $matchKey
     * @return array|\ReflectionMethod[]|null
     */
    public function getMethods(string $matchKey) : ?array;

    /**
     * @param string $matchKey
     * @return array|\ReflectionClass[]|null
     */
    public function getClasses(string $matchKey) : ?array;
}