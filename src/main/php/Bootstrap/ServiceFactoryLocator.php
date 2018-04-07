<?php namespace Motorphp\SilexTools\Bootstrap;

use Motorphp\SilexTools\ClassScanner\ClassFile;
use Motorphp\SilexTools\ClassScanner\Scanner;
use Motorphp\SilexTools\Matcher\FilterMatcher;
use Motorphp\SilexTools\Matcher\MatchResultsCollector;

class ServiceFactoryLocator
{
    /**
     * @var array|string[]
     */
    private $folders;

    /**
     * @var FilterMatcher
     */
    private $matcher;

    public function locate(Scanner $scanner)
    {
        /** @var ClassFile[] $files */
        $files = [];
        foreach ($this->folders as $folder) {
            $found = $scanner->scan($folder);
            $files = array_merge($files, $found);
        }

        foreach ($files as $file) {

        }
    }

    private function checkClassFile(ClassFile $file)
    {
        $className = $file->getClassName();

        $matchCollector = new MatchResultsCollector();
        $this->matcher->matchAll($className, $matchCollector);
    }
}