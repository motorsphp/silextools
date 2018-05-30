<?php namespace Motorphp\SilexTools\Components\Annotations;

use Motorphp\SilexTools\Components\Component;
use Motorphp\SilexTools\Components\KeyFactories;
use Swagger\Annotations\Operation;
use Motorphp\SilexTools\Components\Controller;

class ControllerProcessor
{
    public function binding(Operation $annotation, \ReflectionMethod $reflector) : Controller\Binding
    {
        $params = [];
        foreach ($reflector->getParameters() as $parameter) {
            $type = $parameter->getType();
            if ($type) {
                $resolvedType = $this->resolveType($type);
                $name = $parameter->getName();
                $params[$name] = $resolvedType;
            }
        }

        return new Controller\Binding(
            $annotation->operationId,
            $annotation->method,
            $annotation->path,
            $params,
            $reflector
        );
    }

    private function resolveType(\ReflectionType $type) : string
    {
        $typeName = $type->getName();
        if ($type->isBuiltin()) {
            return $typeName;
        }

        try {
            $reflectionClass = new \ReflectionClass($typeName);
            return $reflectionClass->getName();
        } catch (\ReflectionException $e) {
            return $typeName;
        }
    }

    /**
     * @param array | Controller\Binding[] $bindings
     * @param KeyFactories $keysBuilder
     * @return array|Component[]
     */
    public function components(array $bindings, KeyFactories $keysBuilder) : array
    {
        $components = [];
        foreach ($bindings as $binding) {
            $components[] = $binding->configureBuilder(new Controller\Builder())
                ->withCallback(
                    $binding->resolveKey($keysBuilder),
                    $binding->getMethod()
                )->build();
        }

        return $components;
    }
}