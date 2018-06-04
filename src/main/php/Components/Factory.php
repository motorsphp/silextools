<?php namespace Motorphp\SilexTools\Components;

use Motorphp\SilexTools\Components\Factory\Capabilities;
use Motorphp\SilexTools\Components\Factory\Placement;

interface Factory
{
    function writeKey(SourceCodeWriter $writer);

    function writeCallback(SourceCodeWriter $writer);

    public function getPlacement(): Placement;

    public function getCapabilities(): Capabilities;
}