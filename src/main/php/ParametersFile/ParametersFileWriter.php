<?php namespace Motorphp\SilexTools\ParametersFile;

use Motorphp\SilexTools\Components\ComponentsVisitorAbstract;
use Motorphp\SilexTools\Components\Parameter;
use Motorphp\SilexTools\Components\ServiceCallback;

class ParametersFileWriter extends ComponentsVisitorAbstract
{
    private $lines = [];

    function visitParameter(ServiceCallback $callback, Parameter $service)
    {
        $writer = new ValueWriter();
        $this->lines[] = sprintf("%s=%s", $service->writeName($writer)->asLiteral(), $service->writeValue($writer)->asLiteral());
    }

    function done() : string
    {
        $lines = array_merge([], $this->lines);
        sort($lines);
        return implode("\n\n", $lines);
    }

}
