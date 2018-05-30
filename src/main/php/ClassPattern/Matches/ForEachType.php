<?php namespace Motorphp\SilexTools\ClassPattern\Matches;

class ForEachType
{
    private $type;

    private $handler;

    public function __construct(string $type, Handler $handler)
    {
        $this->type = $type;
        $this->handler = $handler;
    }

    public function withCallback(string $patternId, \Closure $closure) : ForEachType
    {
        $type = $this->type;
        $callback = function (PatternMatches $matches) use ($type, $closure) {
            $matches->each($closure, $type);
        };

        $this->handler->withCallback($patternId, $callback);
        return $this;
    }

    public function done() : Handler
    {
        return $this->handler;
    }
}