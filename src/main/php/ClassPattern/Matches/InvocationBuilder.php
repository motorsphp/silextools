<?php namespace Motorphp\SilexTools\ClassPattern\Matches;

class InvocationBuilder
{
    /** @var \Closure */
    private $closure;

    /** @var Handler */
    private $handler;

    /**
     * Callback constructor.
     * @param \Closure $closure
     * @param Handler $handler
     */
    public function __construct(\Closure $closure, Handler $handler)
    {
        $this->closure = $closure;
        $this->handler = $handler;
    }

    public function when(string $type, string $patternId) : InvocationBuilder
    {
        $closure = $this->closure;
        $callback = function (PatternMatches $matches) use ($type, $closure) {
            $matches->each($closure, $type);
        };

        $this->handler->withCallback($patternId, $callback);
        return $this;
    }

    public function invoke(\Closure $closure) : InvocationBuilder
    {
        return $this->handler->invoke($closure);
    }

    public function done() : Handler
    {
        return $this->handler;
    }
}