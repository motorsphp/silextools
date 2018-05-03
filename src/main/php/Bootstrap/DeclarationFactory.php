<?php namespace Motorphp\SilexTools\Bootstrap;

use Motorphp\SilexTools\NetteLibrary\MethodBodyPart;
use Motorphp\SilexTools\NetteLibrary\PhpLiterals;
use Nette\PhpGenerator\PhpLiteral;

class DeclarationFactory implements Declaration
{
    /**
     * @var string|PhpLiteral
     */
    private $key;

    /**
     * @var PhpLiteral
     */
    private $factory;

    private $imports = [];

    public function withKeyFromString(string $key) : DeclarationFactory
    {
        $this->key = $key;
        return $this;
    }
    public function withKeyFromClass(\ReflectionClass $reflection) : DeclarationFactory
    {
        $this->imports[] = $reflection;
        $this->key = PhpLiterals::className($reflection);
        return $this;
    }

    public function withKeyFromConstant(\ReflectionClassConstant $reflection) : DeclarationFactory
    {
        $declaringClass = $reflection->getDeclaringClass();
        $this->imports[] = $declaringClass;

        $this->key = PhpLiterals::constant($reflection);
        return $this;
    }

    public function withFactoryFromMethod(\ReflectionMethod $reflection) : DeclarationFactory
    {
        $declaringClass = $reflection->getDeclaringClass();
        $this->imports[] = $declaringClass;

        $this->factory = PhpLiterals::staticMethod($reflection);
        return $this;
    }

    public function canBuild(): bool
    {
        return !empty($this->key) && !empty($this->factory);
    }

    /**
     * @return MethodBodyPart
     */
    public function build(): MethodBodyPart
    {
        $bodyPartTemplate = <<<'EOT'
$container->offsetSet(?, function (Container $container) {
    return ?($container);
});

EOT;
        return new MethodBodyPart($bodyPartTemplate, [$this->key, $this->factory], $this->imports);
    }
}