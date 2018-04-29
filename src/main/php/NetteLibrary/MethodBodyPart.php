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
     * @var array|\ReflectionClass[]
     */
    private $imports;

    /**
     * MethodBodyPart constructor.
     * @param string $body
     * @param array $args
     * @param array|\ReflectionClass[] $imports
     */
    public function __construct(string $body, array $args, array $imports)
    {
        $this->body = $body;
        $this->args = $args;
        $this->imports = $imports;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * @return array|\ReflectionClass[]
     */
    public function getImports()
    {
        return $this->imports;
    }



    public function merge(MethodBodyPart $other): MethodBodyPart
    {
        $body = $this->body . $other->body;
        $args = array_merge($this->args, $other->args);
        $imports = array_merge($this->imports, $other->imports);


        return new MethodBodyPart($body, $args, $imports);
    }

    public function configure(Method $method)
    {
        $method->setBody($this->body, $this->args);
    }


}
