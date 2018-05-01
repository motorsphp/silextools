<?php namespace Motorphp\SilexTools\Bootstrap;

use Motorphp\SilexTools\NetteLibrary\MethodBodyPart;
use Nette\PhpGenerator\PhpLiteral;

class DeclarationProvider implements Declaration
{
    /**
     * @var \ReflectionClass
     */
    private $import;

    /**
     * @var PhpLiteral
     */
    private $provider;

    public function setProviderFromClass(\ReflectionClass $reflection) : DeclarationProvider
    {
        $this->import = $reflection;
        $this->provider = $reflection->getShortName();
        $this->provider = new PhpLiteral($this->provider);

        return $this;
    }

    public function canBuild(): bool
    {
        return !empty($this->provider);
    }

    /**
     * @return MethodBodyPart
     */
    public function build(): MethodBodyPart
    {
        $bodyPartTemplate = <<<'EOT'
$container->register(new ?());


EOT;
        return new MethodBodyPart($bodyPartTemplate, [$this->provider], [$this->import]);
    }
}