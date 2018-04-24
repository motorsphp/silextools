<?php namespace Motorphp\SilexTools\Bootstrap;

use Motorphp\SilexTools\NetteLibrary\MethodBodyPart;
use Motorphp\SilexTools\NetteLibrary\PhpLiterals;
use Nette\PhpGenerator\PhpLiteral;

class DeclarationRoute implements Declaration
{
    /**
     * @var string[]
     */
    private $import = [];

    /** @var string */
    private $httpPath;

    /** @var string */
    private $httpMethod;

    /** @var string */
    private $serviceMethod;

    /** @var PhpLiteral */
    private $serviceKey;

    private $paramConverters;

    public function build(BootstrapMethodBuilder $builder): BootstrapMethodBuilder
    {
        $template = <<<'EOT'
$serviceMethod = implode(':', [?, ?]);
$controllers->?(?, $serviceMethod);
EOT;
        $params = [$this->serviceKey, $this->serviceMethod, $this->httpMethod, $this->httpPath];

        $builder->addMethodBody(new MethodBodyPart($template, $params));
        $builder->addImports($this->import);
        return $builder;
    }

    public function canBuild(): bool
    {
        return true;
    }

    public function withHttpPath(string $path): DeclarationRoute
    {
        $this->httpPath = $path;
        return $this;
    }

    public function withHttpMethod(string $method): DeclarationRoute
    {
        $this->httpMethod = $method;
        return $this;
    }

    public function withServiceHandler(\ReflectionMethod $reflection): DeclarationRoute
    {
        $this->serviceKey = PhpLiterals::classLiteral(
            $reflection->getDeclaringClass()->getShortName()
        );
        $this->serviceMethod = $reflection->getShortName();
        $this->import[] = $reflection->getDeclaringClass()->getNamespaceName();

        return $this;
    }
}