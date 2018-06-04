<?php namespace Motorphp\SilexTools\NetteLibrary\SourceCode;

use Motorphp\SilexTools\Components\SourceCodeWriter;
use Motorphp\SilexTools\Components\Value;
use Nette\PhpGenerator\PhpLiteral;

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

    public function writeString(string $value) : Value
    {
        $this->params[] = $value;
        return new Value($value);
    }

    public function writeClassType(\ReflectionClass $value) : Value
    {
        $literal = $value->getShortName();

        $this->params[] = new PhpLiteral($literal);
        $this->imports[] = $value->getName();
        return new Value($literal);
    }

    public function writeClassName(\ReflectionClass $value) : Value
    {
        $literal = $value->getShortName() . '::' . 'class';
        $this->params[] = new PhpLiteral($literal);
        $this->imports[] = $value->getName();
        return new Value($literal);
    }

    public function writeConstant(\ReflectionClassConstant $value) : Value
    {
        $literal = $value->getDeclaringClass()->getShortName() . '::' . $value->getName();

        $this->params[] = new PhpLiteral($literal);
        $this->imports[] = $value->getDeclaringClass()->getName();

        return new Value($literal);
    }

    public function writeStaticInvocation(\ReflectionMethod $value) : Value
    {
        $literal = $value->getDeclaringClass()->getShortName() . '::' . $value->getName();
        $this->params[] = new PhpLiteral($literal);
        $this->imports[] = $value->getDeclaringClass()->getName();

        return new Value($literal);
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