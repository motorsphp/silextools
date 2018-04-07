<?php namespace Motorphp\SilexTools\Bootstrap;

use Motorphp\SilexAnnotations\Common\ContainerKey;
use Motorphp\SilexAnnotations\Common\ServiceFactory;
use Motorphp\SilexAnnotations\Reader\ConstantsReader;

class ServiceFactoryDeclarationsBuilder
{
    /**
     * @var ConstantsReader
     */
    private $reader;

    public function addKey(\ReflectionClassConstant $reflection)
    {
        /** @var ContainerKey $annotation */
        $annotation = $this->reader->getConstantAnnotation($reflection, ContainerKey::class);
        if (empty($annotation->for)) {
            throw new \DomainException('ContainerKey can not be matched to a service');
        }
    }

    public function addFactory(\ReflectionMethod $reflection)
    {
        /** @var ServiceFactory $annotation */
        $annotation = $this->reader->getMethodAnnotation($reflection, ServiceFactory::class);

        $containerKey = $annotation->containerKey;
        if (empty($containerKey)) { // infer the container key from the return type
            $returnType = $reflection->getReturnType();
            if (!is_null($returnType)) {
                $containerKey = $returnType->getName();
            }
        }

        if (empty($containerKey)) {
            throw new \DomainException('could not infer the container key');
        }
    }
}