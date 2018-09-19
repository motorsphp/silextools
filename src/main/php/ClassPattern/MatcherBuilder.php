<?php namespace Motorphp\SilexTools\ClassPattern;

interface MatcherBuilder
{
  function setAppliesTo(string $reflectorType): MatcherBuilder;

  function addMatchLabel($key): MatcherBuilder;

  function build(): Matcher;
}