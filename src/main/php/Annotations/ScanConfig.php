<?php namespace Motorphp\SilexTools\Annotations;

use Motorphp\PhpScan\ObjectQueryStream\ConsumerBuilder;

interface ScanConfig
{
    function providersConsumer(ConsumerBuilder $builder) : ?ConsumerBuilder;

    function controllersConsumer(ConsumerBuilder $builder) : ?ConsumerBuilder;

    function factoriesConsumer(ConsumerBuilder $builder) : ?ConsumerBuilder;

    function convertersConsumer(ConsumerBuilder $builder) : ?ConsumerBuilder;

    function parametersConsumer(ConsumerBuilder $builder) : ?ConsumerBuilder;

    /**
     * @return array|string[]
     */
    function getFolders(): array;
}