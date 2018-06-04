<?php namespace Motorphp\SilexTools\NetteLibrary\RouteAdapters;

use Motorphp\SilexTools\Components\ComponentsVisitorAbstract;
use Motorphp\SilexTools\Components\Controller;
use Motorphp\SilexTools\Components\Converter;
use Motorphp\SilexTools\Components\ServiceCallback;

use Motorphp\SilexTools\NetteLibrary\Method\MethodBody;
use Motorphp\SilexTools\NetteLibrary\SourceCode\Fragment;
use Motorphp\SilexTools\NetteLibrary\SourceCode\FragmentWriter;

class MethodBodyWriter extends ComponentsVisitorAbstract
{
    private static $template = <<<'EOT'
$controllers->?(?, sprintf('%s:%s', ?, ?))
EOT;

    private static $providerTemplate = <<<'EOT'
->convert(?, sprintf('%s:%s', ?, ?))
EOT;

    private $controllerParts = [];

    private $providerParts = [];

    function visitController(ServiceCallback $callback, Controller $service)
    {
        $writer = FragmentWriter::fromTemplate(MethodBodyWriter::$template);

        $service->writeHttpMethod($writer);
        $service->writeEndpoint($writer);

        $callback->writeKey($writer);
        $callback->writeMethod($writer);

        $this->controllerParts[] = [
            $service->getOperationId(), $writer->done()
        ];
    }

    function visitConverter(ServiceCallback $callback, Converter $service)
    {
        /** @var $writer */
        $writer = FragmentWriter::fromTemplate(MethodBodyWriter::$providerTemplate);
        $service->writeName($writer);

        $callback->writeKey($writer);
        $callback->writeMethod($writer);

        $this->providerParts[] = [
            $service->getOperationId(),
            $writer->done()
        ];
    }

    private function mergeParts()
    {
        $groups = array_reduce($this->controllerParts, function ($carry, array $pair) {
            list($operation, $controller) = $pair;
            $carry[$operation] = [$controller];
            return $carry;
        }, []);
        $merged = [];
        foreach ($this->providerParts as $entry) {
            list($operation, $provider) = $entry;
            $groups[$operation][] = $provider;
        }

        foreach ($groups as $list) {
            $reducer = function (Fragment $controller, Fragment $provider) {
                return $controller->append($provider);
            };
            $controller = array_shift($list);
            $merged[] = array_reduce($list, $reducer, $controller);
            $merged[] = new Fragment(';' . PHP_EOL, [], []);
        }

        return $merged;
    }

    function getMethodBody() : MethodBody
    {
        $bodyParts = $this->mergeParts();
        return new MethodBody($bodyParts);
    }

}