<?php namespace Motorphp\SilexTools\NetteLibrary;

use Nette\PhpGenerator\Method;

class MethodBodyPart
{
    /**
     * @var string
     */
    private $body;

    /**
     * @var array
     */
    private $args;

    /**
     * MethodBodyPart constructor.
     * @param string $body
     * @param array $args
     */
    public function __construct(string $body, array $args)
    {
        $this->body = $body;
        $this->args = $args;
    }

    public function merge(MethodBodyPart $other): MethodBodyPart
    {
        $body = $this->body . $other->body;
        $args = array_merge($this->args, $other->args);

        return new MethodBodyPart($body, $args);
    }

    public function configure(Method $method)
    {
        $method->setBody($this->body, $this->args);
    }

}
