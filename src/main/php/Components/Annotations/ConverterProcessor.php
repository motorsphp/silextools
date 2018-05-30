<?php namespace Motorphp\SilexTools\Components\Annotations;

use Motorphp\SilexAnnotations\Common;
use Motorphp\SilexTools\Components\Component;
use Motorphp\SilexTools\Components\KeyFactories;
use Motorphp\SilexTools\Components\ServiceCallback;
use Motorphp\SilexTools\Components\Converter;
use Motorphp\SilexTools\Components\Controller;

class ConverterProcessor
{
    public function binding(Common\ParamConverter $annotation, \ReflectionMethod $reflector) : Converter\Binding
    {
        $operationId = empty($annotation->operation) ? "" : (string) $annotation->operation;

        $typeName = $reflector->getReturnType()->getName();
        try {
            $typeClass = new \ReflectionClass($typeName);
            $typeName = $typeClass->getName();
        } catch (\ReflectionException $e) { }


        if ($annotation->service) {
            $callback = new ServiceCallback\BindingScalarKey($annotation->service, $reflector);
        } else {
            $callback = ServiceCallback\Bindings::classname($reflector);
        }

        return new Converter\Binding($typeName, trim($operationId) , $callback);
    }

    /**
     * @param array | Converter\Binding[] $bindings
     * @param array | Controller\Binding[] $controllerBindings
     * @param KeyFactories $keysBuilder
     * @return array|Component[]
     */
    public function components(array $bindings, array $controllerBindings, KeyFactories $keysBuilder) : array
    {
        $components = [];
        foreach ($bindings as $binding) {
            foreach ($controllerBindings as $controllerBinding) {
                $processed = $this->processPair($binding, $controllerBinding, $keysBuilder);
                foreach ($processed as $component) {
                    array_push($components, $component);
                }
            }
        }

        return $components;
    }

    /**
     * @param Converter\Binding $binding
     * @param Controller\Binding $controllerBinding
     * @param KeyFactories $keysBuilder
     * @return array|Component[]
     */
    function processPair(Converter\Binding $binding, Controller\Binding $controllerBinding, KeyFactories $keysBuilder): array
    {
        $components = [];
        foreach ($controllerBinding->getParams() as $param) {
            $isBindingMatch = $binding
                ->configurePatternBuilder(new Controller\ParamPatternBuilder())
                ->build()
                ->matches($param)
            ;

            if ($isBindingMatch) {
                $builder = new Converter\Builder();
                $components[] = $builder
                    ->setParam($param)
                    ->withCallback(
                        $binding->resolveKey($keysBuilder),
                        $binding->getMethod()
                    )
                    ->build();
            }
        }

        return $components;
    }
}