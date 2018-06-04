<?php namespace Motorphp\SilexTools\Components\Factory;

class Capabilities
{
    /** @var array  */
    private $map = [];

    /**
     * Capabilities constructor.
     * @param array $map
     */
    public function __construct(array $map)
    {
        $this->map = $map;
    }

    function getFirewall() : ?string
    {
        if (array_key_exists('firewall', $this->map)) {
            return $this->map['firewall'];
        }

        return null;
    }
}