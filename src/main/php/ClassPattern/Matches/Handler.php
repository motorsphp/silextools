<?php namespace Motorphp\SilexTools\ClassPattern\Matches;


use Motorphp\SilexTools\ClassPattern\Matches;

class Handler
{
    /** @var array|PatternHandler[] */
    private $handlers = [];

    public function invoke(\Closure $closure) : InvocationBuilder
    {
        return new InvocationBuilder($closure, $this);
    }

    public function forEachType(string $type) : ForEachType
    {
        return new ForEachType($type, $this);
    }

    public function forEach(string $patternId, string $type) : EachType
    {
        return new EachType($type, $patternId, $this);
    }

    public function withCallback(string $patternId, \Closure $closure) : Handler
    {
        $this->handlers[] = new CallbackHandler($patternId, $closure);
        return $this;
    }

    public function withHandler(PatternHandler $handler) : Handler
    {
        $this->handlers[] = $handler;
        return $this;
    }

    public function handle(Matches $matches)
    {
        $patterns = array_unique(
            array_map(function (PatternHandler $handler) {
                return $handler->getPatternId();
            }, $this->handlers)
        );

        $groups = $this->groupMatches($patterns, $matches);
        foreach ($groups as $group) {
            foreach ($this->handlers as $handler) {
                if ($handler->getPatternId() === $group->getPatternId()) {
                    $handler->handle($group);
                }
            }
        }
    }

    /**
     * @param array $patterns
     * @param Matches $matches
     * @return array|PatternMatches[]
     */
    private function groupMatches(array $patterns, Matches $matches) : array
    {
        $groups = [];
        foreach($patterns as $patternId) {
            $groups[] = $this->createPatternMatches($patternId, $matches);
        }

        return array_filter($groups, function (PatternMatches $list) {
            return !$list->isEmpty();
        });
    }

    private function createPatternMatches(string $patternId, Matches $matches) : PatternMatches
    {
        $patternMatches = new PatternMatches($patternId);

        $list = $matches->getConstants($patternId);
        $patternMatches->setConstants($list);

        $list = $matches->getClasses($patternId);
        $patternMatches->setClasses($list);

        $list = $matches->getMethods($patternId);
        $patternMatches->setMethods($list);

        $list = $matches->getParams($patternId);
        $patternMatches->setParams($list);

        return $patternMatches;
    }

}