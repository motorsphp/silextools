<?php namespace Motorphp\SilexTools\NetteLibrary\Methods;

use Motorphp\SilexTools\Components\ComponentsVisitorAbstract;
use Motorphp\SilexTools\Components\Factory;
use Motorphp\SilexTools\Components\Provider;
use Motorphp\SilexTools\NetteLibrary\Method\BodyWriter;
use Motorphp\SilexTools\NetteLibrary\Method\MethodBody;
use Motorphp\SilexTools\NetteLibrary\SourceCode\FragmentWriter;

class BodyWriterFactory extends ComponentsVisitorAbstract implements BodyWriter
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
     * @param \ReflectionMethod $signature
     * @return BodyWriterFactory
     * @throws \ReflectionException
     */
    public static function fromSignature(\ReflectionMethod $signature) : BodyWriterFactory
    {
        $containerType = null;
        foreach ($signature->getParameters() as $parameter) {
            if ($parameter->getName() === 'container') {
                $containerType = $parameter->getType();
            }
        }

        if ($containerType && !$containerType->isBuiltin()) {
            $containerType = new \ReflectionClass($containerType->getName());
            return new BodyWriterFactory($containerType);
        }

        return new BodyWriterFactory();
    }

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
        if (! $component->getPlacement()->isStandalone()) {
            return;
        }

        $writer = FragmentWriter::fromTemplate(BodyWriterFactory::$template);
        $component->writeKey($writer);
        if ($this->containerClass) {
            $writer->writeClassType($this->containerClass);
        } else {
            $writer->writeString("");
        }
        $component->writeCallback($writer);

        $this->bodyParts[] = $writer->done();
    }

    function getMethodBody() : MethodBody
    {
        return new MethodBody($this->bodyParts);
    }

}