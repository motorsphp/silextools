<?php namespace Motorphp\SilexTools\Components\Key;

use Motorphp\SilexTools\Components\Key;
use Motorphp\SilexTools\Components\KeyFactories;

class LookupFactories implements KeyFactories
{
    /** @var KeyFactories */
    private $factory;

    /** @var array | Key[] */
    private $implicitKeys;

    /**
     * LookupFactories constructor.
     * @param KeyFactories $factory
     * @param array|Key[] $implicitKeys
     */
    public function __construct(KeyFactories $factory, $implicitKeys)
    {
        $this->factory = $factory;
        $this->implicitKeys = $implicitKeys;
    }

    function fromString(string $source): Key
    {
        $key = $this->factory->fromString($source);
        $existing = $this->findImplicitMatch($key);
        return empty($existing) ? $key : $existing;
    }

    function fromClassName(\ReflectionClass $source): Key
    {
        $key = $this->factory->fromClassName($source);
        $existing = $this->findImplicitMatch($key);
        return empty($existing) ? $key : $existing;
    }

    function fromConstant(\ReflectionClassConstant $source): Key
    {
        $key = $this->factory->fromConstant($source);
        $existing = $this->findImplicitMatch($key);
        return empty($existing) ? $key : $existing;
    }

    private function findImplicitMatch(Key $key) : ?Key
    {
        foreach ($this->implicitKeys as $implicitKey) {
            if ($implicitKey->getId() === $key->getId()) {
                return $implicitKey;
            }
        }

        return null;
    }
}