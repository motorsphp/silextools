<?php namespace Motorphp\SilexTools\NetteLibrary\Method;

class Configuration
{
    /** @var BodyWriter */
    private $writer;

    /** @var \ReflectionMethod */
    private $signature;

    /**
     * @return BodyWriter
     */
    public function getWriter(): BodyWriter
    {
        return $this->writer;
    }

    /**
     * @param BodyWriter $writer
     */
    public function setWriter(BodyWriter $writer): void
    {
        $this->writer = $writer;
    }

    /**
     * @return \ReflectionMethod
     */
    public function getSignature(): \ReflectionMethod
    {
        return $this->signature;
    }

    /**
     * @param \ReflectionMethod $signature
     */
    public function setSignature(\ReflectionMethod $signature): void
    {
        $this->signature = $signature;
    }
}