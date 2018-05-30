<?php namespace Motorphp\SilexTools\ClassPattern\Matches;

class CallbackHandler implements PatternHandler
{
    /** @var string  */
    private $patternId;

    /** @var \Closure  */
    private $closure;

    /**
     * MatchesHandler constructor.
     * @param string $patternId
     * @param \Closure $closure
     */
    public function __construct(string $patternId, \Closure $closure)
    {
        $this->patternId = $patternId;
        $this->closure = $closure;
    }

    function getPatternId(): string
    {
        return $this->patternId;
    }

    function handle(PatternMatches $matches)
    {
        $this->assertCanInvoke($matches->getPatternId());
        $closure = $this->closure;
        $closure($matches);
    }

    private function assertCanInvoke(string $patternId) : bool
    {
        if ($this->patternId === $patternId) {
            return true;
        }

        $message = sprintf('patternId not equal to %s', $this->patternId);
        throw new \RuntimeException($message);
    }
}