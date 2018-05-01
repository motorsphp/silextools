<?php namespace Motorphp\SilexTools\ClassScanner;

use Symfony\Component\Finder\Finder;

class Scanner
{
    /**
     * @param array | string[] $directory
     * @param Finder $finder
     * @return ClassFile[]
     */
    public function scanUseFinder(array $directory, Finder $finder):array
    {
        $finder
            ->files()
            ->name('*.php')
            ->in($directory)
        ;

        $classFiles = [];
        /** @var \SplFileInfo $file */
        foreach ($finder as $file) {
            $parser = new ClassFileParser();
            $classFile = $parser->parseSplFile($file);
            if ($classFile) {
                $classFiles[] = $classFile;
            }
        }

        return $classFiles;
    }

    /**
     * @param string $directory
     * @return ClassFile[]
     */
    public function scan(string $directory):array
    {
        return $this->scanAll([$directory]);
    }

    /**
     * @param array | string[] $directory
     * @return ClassFile[]
     */
    public function scanAll(array $directory):array
    {
        $finder = new Finder();
        return $this->scanUseFinder($directory, $finder);
    }
}
