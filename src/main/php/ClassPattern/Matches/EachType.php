<?php namespace Motorphp\SilexTools\ClassPattern\Matches;

class EachType
{
    private $type;

    private $patternId;

    private $handler;

    public function __construct(string $type, string $patternId, Handler $handler)
    {
        $this->type = $type;
        $this->patternId = $patternId;
        $this->handler = $handler;
    }

    public function withCallback(\Closure $closure) : Handler
    {
        $type = $this->type;
        $callback = function (PatternMatches $matches) use ($type, $closure) {
            $matches->each($closure, $type);
        };

        $this->handler->withCallback($this->patternId, $callback);
        return $this->handler;
    }
}