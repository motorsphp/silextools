<?php namespace Motorphp\SilexTools\NetteLibrary\Method;

use Motorphp\SilexTools\Components\Key;
use Motorphp\SilexTools\Bootstrap\ReflectorVisitor;
use Motorphp\SilexTools\Components\ReflectorVisitorDefault;
use Motorphp\SilexTools\Components\SourceCodeWriter;
use Motorphp\SilexTools\NetteLibrary\PhpLiterals;
use Nette\PhpGenerator\PhpLiteral;

class MethodBodyPartWriter implements SourceCodeWriter
{
    /** @var string */
    private $template;

    private $params = [];

    /** @var array| string[]  */
    private $imports = [];

    public static function fromTemplate(string $template) : MethodBodyPartWriter
    {
        $builder = new MethodBodyPartWriter();
        $builder->template = $template;

        return $builder;
    }

    function build() : MethodBodyPart
    {
        return new MethodBodyPart($this->template, $this->params, $this->imports);
    }

    public function writeString(string $value)
    {
        $this->params[] = $value;
        return $this;
    }

    public function writeClassName(\ReflectionClass $value)
    {
        $this->params[] = PhpLiterals::className($value);
        $this->imports[] = $value->getName();
        return $this;
    }

    public function writeClassType(\ReflectionClass $value)
    {
        $this->params[] = PhpLiterals::class($value);
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
}