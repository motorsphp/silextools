<?php namespace Motorphp\SilexTools\Annotations;

use Motorphp\PhpScan\Criteria\Criteria;
use Motorphp\PhpScan\ObjectQueryStream\ConsumerBuilder;
use Motorphp\PhpScan\ObjectQueryStream\Consumers;
use Motorphp\SilexAnnotations\Common\ParamConverter;
use Motorphp\SilexAnnotations\Common\Parameter;
use Motorphp\SilexAnnotations\Common\ServiceFactory;
use Pimple\ServiceProviderInterface;
use Swagger\Annotations\Delete;
use Swagger\Annotations\Get;
use Swagger\Annotations\Post;
use Swagger\Annotations\Put;

class ScanConfigDefault implements ScanConfig
{
    private $folders = [];

    private $providers;

    private $controllers;

    private $factories;

    private $converters;

    private $parameters;

    static public function instance($folders) : ScanConfig
    {
        $config = is_string($folders) ? new ScanConfigDefault([$folders]) : new ScanConfigDefault($folders);
        $config
            ->providers(ServiceProviderInterface::class)
            ->controllers([Get::class, Post::class, Put::class, Delete::class])
            ->factories()
            ->converters()
        ;
        return $config;
    }

    public function __construct(array $folders)
    {
        $this->folders = $folders;
    }

    public function providers($interfaces) : ScanConfigDefault
    {
        $this->providers = $interfaces;
        return $this;
    }

    /**
     * @param ConsumerBuilder $builder
     * @return ConsumerBuilder|null
     * @throws \Exception
     */
    public function providersConsumer( ConsumerBuilder $builder) : ?ConsumerBuilder
    {
        if ($this->providers) {
            $query = Criteria::factory(\ReflectionClass::class, 'c')
                ->where(
                //           Consumers::expr()->implements('c', ServiceProviderInterface::class)
                    Consumers::expr()->implements('c', $this->providers)
                )
                ->build()
            ;
            return $builder->setQuery($query);
        }

        return null;
    }

    public function controllers($interfaces) : ScanConfigDefault
    {
        $this->controllers = $interfaces;
        return $this;
    }

    /**
     * @param ConsumerBuilder $builder
     * @return ConsumerBuilder|null
     * @throws \Exception
     */
    public function controllersConsumer( ConsumerBuilder $builder) : ?ConsumerBuilder
    {
        if ($this->controllers) {
            $query = Criteria::factory(\ReflectionMethod::class, 'c')
                ->andWhere(
                //Consumers::expr()->hasAnyAnnotations('c', [Get::class, Post::class, Put::class, Delete::class])
                    Consumers::expr()->hasAnyAnnotations('c', $this->controllers)
                )
                ->build()
            ;
            return $builder->setQuery($query);
        }
    }

    public function factories() : ScanConfigDefault
    {
        $this->factories = true;
        return $this;
    }

    /**
     * @param ConsumerBuilder $builder
     * @return ConsumerBuilder|null
     * @throws \Exception
     */
    public function factoriesConsumer( ConsumerBuilder $builder) : ?ConsumerBuilder
    {
        if ($this->factories) {
            $query = Criteria::factory(\ReflectionMethod::class, 'c')
                ->andWhere(
                    Consumers::expr()->hasAnnotations('c', ServiceFactory::class),
                    Consumers::expr()->hasModifiers('c', \ReflectionMethod::IS_STATIC )
                )
                ->build()
            ;
            return $builder->setQuery($query);
        }
    }

    public function converters() : ScanConfigDefault
    {
        $this->converters = true;
        return $this;
    }

    /**
     * @param ConsumerBuilder $builder
     * @return ConsumerBuilder|null
     * @throws \Exception
     */
    public function convertersConsumer( ConsumerBuilder $builder) : ?ConsumerBuilder
    {
        if ($this->converters) {
            $query = Criteria::factory(\ReflectionMethod::class, 'c')
                ->andWhere(
                    Consumers::expr()->hasAnnotations('c', ParamConverter::class)
                )
                ->build()
            ;
            return $builder->setQuery($query);
        }

        return null;
    }

    public function parameters() : ScanConfigDefault
    {
        $this->parameters = true;
        return $this;
    }

    /**
     * @param ConsumerBuilder $builder
     * @return ConsumerBuilder|null
     * @throws \Exception
     */
    public function parametersConsumer( ConsumerBuilder $builder) : ?ConsumerBuilder
    {
        if ($this->parameters) {
            $query =  Criteria::factory(\ReflectionMethod::class, 'c')
                ->andWhere(
                    Consumers::expr()->hasAnnotations('c', Parameter::class)
                )
                ->build()
            ;
            return $builder->setQuery($query);
        }

        return null;
    }

    function getFolders(): array
    {
        return $this->folders;
    }
}
