<?php namespace Motorphp\SilexTools\NetteLibrary\ProviderAdapters;

use Motorphp\SilexTools\Components\ComponentsVisitorAbstract;
use Motorphp\SilexTools\Components\Provider;
use Motorphp\SilexTools\NetteLibrary\Method\MethodBody;
use Motorphp\SilexTools\NetteLibrary\Method\MethodBodyPartWriter;

class MethodBodyWriter extends ComponentsVisitorAbstract
{
    private static $template = <<<'EOT'
$container->register(new ?());
EOT
    .PHP_EOL;

    private $bodyParts = [];

    function visitProvider(Provider $component)
    {
        $writer = MethodBodyPartWriter::fromTemplate(MethodBodyWriter::$template);
        $component->writeClass($writer);

        $this->bodyParts[] = $writer->build();
    }

    function getMethodBody() : MethodBody
    {
        return new MethodBody($this->bodyParts);
    }

}