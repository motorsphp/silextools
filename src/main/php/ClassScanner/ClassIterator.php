<?php namespace Motorphp\SilexTools\ClassScanner;

class ClassIterator implements \Iterator
{
    /**
     * @var array|ClassFile[]
     */
    private $classFiles;

    private $hasStarted = false;

    /**
     * @var \ReflectionClass
     */
    private $item;

    public function __construct(array $classFiles)
    {
        $this->classFiles = $classFiles;
    }

    /**
     * @return \ReflectionClass
     */
    public function current()
    {
        if (! $this->hasStarted) {
            $this->next();
            $this->hasStarted = true;
        }

        return $this->item;
    }

    public function next()
    {
        $item = null;
        $file = $this->hasStarted ? reset($this->classFiles) : next($this->classFiles);

        while ($item === null && $file !== null) {
            try {
                $item = new \ReflectionClass($file->getClassName());
            } catch (\ReflectionException $e) {
                $file = next($this->classFiles);
            }
        }

        $this->item = $item;
    }

    public function key()
    {
        return key($this->classFiles);
    }

    public function valid()
    {
        if (! $this->hasStarted) {
            $this->next();
            $this->hasStarted = true;
        }

        $key = $this->key();
        return !is_null($key);
    }

    public function rewind()
    {
        $this->hasStarted = false;
    }
}