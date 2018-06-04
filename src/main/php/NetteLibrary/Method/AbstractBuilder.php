<?php namespace Motorphp\SilexTools\NetteLibrary\Method;

use Motorphp\SilexTools\Bootstrap\BootstrapBuilder;
use Motorphp\SilexTools\NetteLibrary\BootstrapBuilderAdapter;
use Motorphp\SilexTools\NetteLibrary\SourceCode\Fragment;
use Nette\PhpGenerator\Factory;
use Nette\PhpGenerator\Method;

abstract class AbstractBuilder
{
    /** @var \ReflectionMethod */
    private $signature;

    /** @var MethodBody */
    private $body;

    /** @var array | Fragment[] */
    private $bodyParts = [];

    public function withSignature(\ReflectionMethod $signature) : AbstractBuilder
    {
        $this->signature = $signature;
        return $this;
    }

    function setMethodBody(MethodBody $body)
    {
        $this->body = $body;
        $this->bodyParts = [];

        return $this;
    }

    /**
     * @param array | Fragment[] $parts
     * @return AbstractBuilder
     */
    function withBodyParts(array $parts) : AbstractBuilder
    {
        $this->bodyParts = array_merge($this->bodyParts, $parts);
        $this->body = null;
        return $this;
    }

    function buildMethodBody() : MethodBody
    {
        return new MethodBody($this->bodyParts);
    }

    protected function configure(BootstrapBuilderAdapter $builder): BootstrapBuilderAdapter
    {
        if ($this->body) {
            $builder->withMethodBody($this->body, $this->signature);
            return $builder;
        }

        $body = $this->buildMethodBody();
        $builder->withMethodBody($body, $this->signature);
        return $builder;
    }
}