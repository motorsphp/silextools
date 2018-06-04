<?php namespace Motorphp\SilexTools\NetteLibrary\SourceCode;

class Fragment
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
     * Fragment constructor.
     * @param string $body
     * @param array $args
     * @param array|\ReflectionClass[] $imports
     */
    public function __construct(string $body, array $args, $imports)
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

    public function append(Fragment $other) : Fragment
    {
        $body = $this->body . $other->getBody();
        $args = array_merge($this->args, $other->getArgs());
        $imports = array_merge($this->imports, $other->getImports());


        return new Fragment($body, $args, $imports);
    }
}