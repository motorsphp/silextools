<?php namespace Motorphp\SilexTools\NetteLibrary;

use Motorphp\SilexTools\Bootstrap\BuildContext;
use Nette\PhpGenerator\Method;

class MethodBody
{
    /**
     * @var MethodBodyPart
     */
    private $parts;

    /**
     * MethodBody constructor.
     * @param array|MethodBodyPart[] $parts
     */
    public function __construct(array $parts)
    {
        $this->parts = $parts;
    }

    public function addAllImports(BuildContext $context) : BuildContext
    {
        $reducer = function (array $imports, MethodBodyPart $part) {
            return array_merge($imports, $part->getImports());
        };

        $imports  = array_reduce($this->parts, $reducer, []);
        $context->addAllImports($imports);

        return $context;
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