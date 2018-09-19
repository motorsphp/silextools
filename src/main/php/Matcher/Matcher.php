<?php namespace Motorphp\SilexTools\Matcher;

use Motorphp\SilexTools\ClassPattern\Constraints\Constraint;
use Motorphp\SilexTools\ClassPattern\Match;
use Motorphp\SilexTools\ClassPattern\Pattern;

class Matcher
{
    public function done(Pattern $pattern, $source)
    {
        $context = new Context();
        $pattern->configureMatchContext($context);

        /** @var \Motorphp\SilexTools\ClassPattern\Matcher[] $matchers */
        $matchers = $context->getMatchers();

        /** @var Constraint[] $constraints */
        $constraints = $context->getConstraints();


        $matches = [];
        while ($token = $source->getToken()) {
            $tokenMatches = [];
            foreach ($matchers as $matcher) {
                $match = $matcher->match($token);
                if ($match instanceof Match) {
                    $tokenMatches[] = $match;
                }
            }

            foreach ($constraints as $constraint) {
                $from = $constraint->getFrom();
                $notMatchedYet = $matches->getNotMatched($from);

                if (0 === $notMatchedYet->size()) {
                    continue;
                }

                if ($constraint->isSatisfied($notMatchedYet)) {
                    $notMatchedYet->markMatched($from);
                }
            }
        }
    }

}