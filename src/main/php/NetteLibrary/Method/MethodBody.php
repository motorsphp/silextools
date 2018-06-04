<?php namespace Motorphp\SilexTools\NetteLibrary\Method;

use Motorphp\SilexTools\NetteLibrary\SourceCode\Fragment;
use Nette\PhpGenerator\Method;

class MethodBody
{
    /**
     * @var Fragment
     */
    private $parts;

    /**
     * MethodBody constructor.
     * @param array|Fragment[] $parts
     */
    public function __construct(array $parts)
    {
        $this->parts = $parts;
    }

    /**
     * @return array | string[]
     */
    public function getImports() : array
    {
        $reducer = function (array $imports, Fragment $part) {
            return array_merge($imports, $part->getImports());
        };

        return array_reduce($this->parts, $reducer, []);
    }

    public function configure(Method $method)
    {
        $args = [];
        $body = '';
        foreach ($this->parts as $part) {
            $body .= $part->getBody();
            $args = array_merge($args, $part->getArgs());
        }

        $method->setBody($body, $args);
    }
}