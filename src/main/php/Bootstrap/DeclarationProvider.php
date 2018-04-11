<?php namespace Motorphp\SilexTools\Bootstrap;

use Motorphp\SilexTools\NetteLibrary\MethodBodyPart;
use Nette\PhpGenerator\PhpLiteral;

class DeclarationProvider
{
    /**
     * @var string
     */
    private $import;

    /**
     * @var PhpLiteral
     */
    private $provider;

    public function setProviderFromClass(\ReflectionClass $reflection) : DeclarationProvider
    {
        $this->import = $reflection->getName();
        $this->provider = $reflection->getShortName();
        $this->provider = new PhpLiteral($this->provider);

        return $this;
    }

    public function canBuild(): bool
    {
        return !empty($this->provider);
    }

    /**
     * @param BootstrapMethodBuilder $builder
     * @return BootstrapMethodBuilder
     */
    public function build(BootstrapMethodBuilder $builder): BootstrapMethodBuilder
    {
        $bodyPartTemplate = <<<'EOT'
$provider = new ?();
$provider->register($container);


EOT;
        $bodyPart = new MethodBodyPart($bodyPartTemplate, [$this->provider]);
        $builder->addMethodBody($bodyPart);

        $builder->addImports([$this->import]);
        return $builder;
    }
}