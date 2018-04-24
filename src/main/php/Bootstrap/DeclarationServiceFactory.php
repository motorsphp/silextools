<?php namespace Motorphp\SilexTools\Bootstrap;

use Motorphp\SilexTools\NetteLibrary\MethodBodyPart;
use Nette\PhpGenerator\PhpLiteral;

class DeclarationServiceFactory implements Declaration
{
    private static $tokens = [
        T_DOUBLE_COLON => '::'
    ];

    /**
     * @var string|PhpLiteral
     */
    private $key;

    /**
     * @var PhpLiteral
     */
    private $factory;

    private $imports = [];

    public function addKeyFromConstant(\ReflectionClassConstant $reflection) : DeclarationServiceFactory
    {
        $declaringClass = $reflection->getDeclaringClass();
        $this->imports[] = $declaringClass->getName();

        $this->key = $declaringClass->getShortName()
            . self::$tokens[T_DOUBLE_COLON]
            . $reflection->getName()
        ;
        $this->key = new PhpLiteral($this->key);

        return $this;
    }

    public function addFactoryFromMethod(\ReflectionMethod $reflection) : DeclarationServiceFactory
    {
        $declaringClass = $reflection->getDeclaringClass();
        $this->imports[] = $declaringClass->getName();

        $this->factory = $declaringClass->getShortName()
            . self::$tokens[T_DOUBLE_COLON]
            . $reflection->getName()
        ;
        $this->factory = new PhpLiteral($this->factory);

        return $this;
    }

    public function canBuild(): bool
    {
        return !empty($this->key) && !empty($this->factory);
    }

    /**
     * @param BootstrapMethodBuilder $builder
     * @return BootstrapMethodBuilder
     */
    public function build(BootstrapMethodBuilder $builder): BootstrapMethodBuilder
    {
        $bodyPartTemplate = <<<'EOT'
$container->offsetSet(?, function (Container $container) {
    return ?($container);
});

EOT;
        $bodyPart = new MethodBodyPart($bodyPartTemplate, [$this->key, $this->factory]);
        $builder->addMethodBody($bodyPart);

        $builder->addImports($this->imports);
        return $builder;
    }
}