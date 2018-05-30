<?php namespace Motorphp\SilexTools\Components\Components;

use Motorphp\SilexAnnotations\Common;
use Motorphp\SilexTools\Components\ServiceCallback;
use Motorphp\SilexTools\Components\Controller;
use Motorphp\SilexTools\Components\Converter;
use Motorphp\SilexTools\Components\Parameter;
use Swagger\Annotations\Operation;

class Bindings
{
    public function parameter(Common\Parameter $annotation, \ReflectionMethod $reflector): Parameter\Binding
    {
        $default = empty($annotation->default) ? "" : $annotation->default;
        $name = empty($annotation->name) ? $reflector->getName() : $annotation->name;

        return new Parameter\Binding($name, $default, $reflector);
    }
    
    public function converter(Common\ParamConverter $annotation, \ReflectionMethod $reflector)  :  Converter\Binding
    {
        $operationId = empty($annotation->operation) ? null : (string) $annotation->operation;

        $typeName = $reflector->getReturnType()->getName();
        try {
            $typeClass = new \ReflectionClass($typeName);
            $typeName = $typeClass->getName();
        } catch (\ReflectionException $e) { }

        $pattern = new Controller\ParamPattern($typeName, $operationId);

        if ($annotation->service) {
            $callback = new ServiceCallback\BindingScalarKey($annotation->service, $reflector);
        } else {
            $callback = ServiceCallback\Bindings::classname($reflector);
        }

        return new Converter\Binding($pattern, $callback);
    }

    public function factory(Common\ServiceFactory $annotation, \ReflectionMethod $reflector) : ServiceCallback\Binding
    {
        if ($annotation->service) {
            return new ServiceCallback\BindingScalarKey($annotation->service, $reflector);
        }

        return ServiceCallback\Bindings::returnType($reflector);
    }

    public function controllerBinding(Operation $annotation, \ReflectionMethod $reflector) : Controller\Binding
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
}