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

    /**
     * @var ServiceCallback
     */
    private $serviceCallback;

    /** @var array|ServiceCallback[]  */
    private $paramConverters = [];

    public function build(): MethodBodyPart
    {
        $controller = $this->buildController();

        $otherParts = [];
        foreach ($this->paramConverters as $name => $callback) {
            $otherParts[] = $this->buildConvert($name, $callback);
        }
        $otherParts[] = new MethodBodyPart(';' . PHP_EOL, [], []);

        $reducer = function (MethodBodyPart $acc, MethodBodyPart $part) {
            return $acc->merge($part);
        };
        $final = array_reduce($otherParts, $reducer, $controller);
        return $final;
    }

    private function buildController()
    {
        $template = <<<'EOT'
$controllers->?(?, implode(':', [?, ?]))
EOT;
        $params = array_merge(
            [$this->httpMethod, $this->httpPath],
            $this->serviceCallback->getParameters()
        );

        return new MethodBodyPart($template, $params, $this->import);
    }

    private function buildConvert(string $paramName, ServiceCallback $callback)
    {
        $template = <<<'EOT'
->convert(?, implode(':', [?, ?]))
EOT;
        $params = array_merge([$paramName], $callback->getParameters());
        return new MethodBodyPart($template, $params, []);
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

    public function withController(\ReflectionMethod $reflection): DeclarationRoute
    {
        $this->serviceCallback = ServiceCallback::fromMethod($reflection);
        $this->import[] = $reflection->getDeclaringClass();

        return $this;
    }

    public function withAllParamConverter(array $converters) : DeclarationRoute
    {
        foreach ($converters as $paramName => $callback) {
            $this->withParamConverter($paramName, $callback);
        }

        return $this;
    }

    public function withParamConverter(string $paramName, ServiceCallback $callback) : DeclarationRoute
    {
        $this->paramConverters[$paramName] = $callback;
        return $this;
    }
}