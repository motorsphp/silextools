<?php namespace Motorphp\SilexTools\Bootstrap;

use Motorphp\SilexTools\ClassScanner\Scanner;
use Motorphp\SilexTools\Matcher\MatcherFactories;

class Builder
{
    public function discoverProviders(string $folder, MatcherFactories $matcher)
    {

    }

    public function discoverServiceFactories(string $folder)
    {
        $scanner = new Scanner();
        $scanner->scan($folder);


    }

    public function discoverControllerFactories(string $folder)
    {

    }

    public function discoverControllerParamFactories(string $folder)
    {

    }

    public function discoverControllers(string $folder, MatcherFactories $matcher)
    {
            // reflection methods
    }

    public function build()
    {

    }
}
