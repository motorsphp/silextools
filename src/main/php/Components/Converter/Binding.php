<?php namespace Motorphp\SilexTools\Components\Converter;

use Motorphp\SilexTools\Components\ServiceCallback;
use Motorphp\SilexTools\Components\Controller;
use Motorphp\SilexTools\Components\Key;
use Motorphp\SilexTools\Components\KeyFactories;

class Binding implements ServiceCallback\Binding
{
    /** @var string */
    private $type;

    /** @var string */
    private $operation;

    /** @var ServiceCallback\Binding */
    private $callbackBinding;

    /**
     * Binding constructor.
     * @param string $type
     * @param string $operation
     * @param ServiceCallback\Binding $callbackBinding
     */
    public function __construct(string $type, string $operation, ServiceCallback\Binding $callbackBinding)
    {
        $this->type = $type;
        $this->operation = $operation;
        $this->callbackBinding = $callbackBinding;
    }

    public function configurePatternBuilder(Controller\ParamPatternBuilder $builder) : Controller\ParamPatternBuilder
    {
        $builder->matchType($this->type);
        if (empty($this->operation)) {
            $builder->matchAnyOperation();
        } else {
            $builder->matchOperation($this->operation);
        }

        return $builder;
    }

    public function resolveKey(KeyFactories $keys): Key
    {
        return $this->callbackBinding->resolveKey($keys);
    }

    public function getMethod(): \ReflectionMethod
    {
        return $this->callbackBinding->getMethod();
    }
}
