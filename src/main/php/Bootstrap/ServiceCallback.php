<?php namespace Motorphp\SilexTools\Bootstrap;

use Motorphp\SilexTools\NetteLibrary\PhpLiterals;
use Nette\PhpGenerator\PhpLiteral;

class ServiceCallback
{
    /**
     * @var string
     */
    private $serviceKey;

    /**
     * @var PhpLiteral
     */
    private $serviceKeyLiteral;

    /**
     * @var string
     */
    private $method;

    public static function fromMethod(\ReflectionMethod $reflection) : ServiceCallback
    {
        $instance = new ServiceCallback();
        $instance
            ->withServiceKeyFromClass($reflection->getDeclaringClass())
            ->method = $reflection->getName()
        ;

        return $instance;
    }

    private function __construct()
    {}

    public function withServiceKey(string $key) : ServiceCallback
    {
        $this->serviceKey = $key;
        return $this;
    }

    public function withServiceKeyFromConstant(\ReflectionClassConstant $reflection) : ServiceCallback
    {
        $this->serviceKeyLiteral = PhpLiterals::constant($reflection);
        return $this;
    }

    public function withServiceKeyFromClass(\ReflectionClass $reflection) : ServiceCallback
    {
        $this->serviceKeyLiteral = PhpLiterals::className($reflection);
        return $this;
    }

    public function getParameters() : array
    {
        if ($this->serviceKeyLiteral) {
            return [$this->serviceKeyLiteral, $this->method];
        }

        return [$this->serviceKey, $this->method];
    }
}