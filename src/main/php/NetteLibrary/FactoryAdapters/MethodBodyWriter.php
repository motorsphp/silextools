<?php namespace Motorphp\SilexTools\NetteLibrary\FactoryAdapters;

use Motorphp\SilexTools\Components\ComponentsVisitorAbstract;
use Motorphp\SilexTools\Components\Factory;
use Motorphp\SilexTools\Components\Provider;
use Motorphp\SilexTools\NetteLibrary\Method\MethodBody;
use Motorphp\SilexTools\NetteLibrary\Method\MethodBodyPartWriter;

class MethodBodyWriter extends ComponentsVisitorAbstract
{
    private static $template = <<<'EOT'
$container->offsetSet(?, function (? $container) {
    return ?($container);
});
EOT
    .PHP_EOL;

    private $bodyParts = [];

    /** @var \ReflectionClass */
    private $containerClass;

    /**
     * MethodBodyWriter constructor.
     * @param \ReflectionClass $containerClass
     */
    public function __construct(\ReflectionClass $containerClass = null)
    {
        $this->containerClass = $containerClass;
    }

    function visitFactory(Factory $component)
    {
        $writer = MethodBodyPartWriter::fromTemplate(MethodBodyWriter::$template);
        $component->writeKey($writer);
        if ($this->containerClass) {
            $writer->writeClassType($this->containerClass);
        } else {
            $writer->writeString("");
        }
        $component->writeCallback($writer);

        $this->bodyParts[] = $writer->build();
    }

    function getMethodBody() : MethodBody
    {
        return new MethodBody($this->bodyParts);
    }

}