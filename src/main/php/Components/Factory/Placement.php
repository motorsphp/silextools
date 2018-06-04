<?php namespace Motorphp\SilexTools\Components\Factory;

use Motorphp\SilexTools\Components\Key;

class Placement
{
    /** @var string */
    private $providerId;

    /**
     * Placement constructor.
     * @param string $providerId
     */
    public function __construct(string $providerId = "")
    {
        $this->providerId = $providerId;
    }

    function isStandalone() : bool
    {
        return empty($this->providerId);
    }

    function atProvider(Key $key) : bool
    {
        return $key->getId() === $this->providerId;
    }
}