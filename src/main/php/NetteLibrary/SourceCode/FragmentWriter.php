<?php namespace Motorphp\SilexTools\NetteLibrary\SourceCode;

use Motorphp\SilexTools\Components\SourceCodeWriter;

class FragmentWriter implements SourceCodeWriter
{
    private $params = [];

    /** @var array| string[]  */
    private $imports = [];

    /** @var string */
    private $template;

    static function fromTemplate(string $template) : FragmentWriter
    {
        $writer = new FragmentWriter();
        $writer->writeTemplate($template);

        return $writer;
    }

    public function writeString(string $value)
    {
        $this->params[] = $value;
        return $this;
    }

    public function writeClassType(\ReflectionClass $value)
    {
        $this->params[] = PhpLiterals::class($value);
        $this->imports[] = $value->getName();
        return $this;
    }

    public function writeClassName(\ReflectionClass $value)
    {
        $this->params[] = PhpLiterals::className($value);
        $this->imports[] = $value->getName();
        return $this;
    }

    public function writeConstant(\ReflectionClassConstant $value)
    {
        $this->params[] = PhpLiterals::constant($value);
        $this->imports[] = $value->getDeclaringClass()->getName();

        return $this;
    }

    public function writeStaticInvocation(\ReflectionMethod $value)
    {
        $this->params[] = PhpLiterals::staticMethod($value);
        $this->imports[] = $value->getDeclaringClass()->getName();
    }

    public function writeTemplate(string $template)
    {
        $this->template = $template;
    }

    public function done() : Fragment
    {
        return new Fragment($this->template, $this->params, $this->imports);
    }
}