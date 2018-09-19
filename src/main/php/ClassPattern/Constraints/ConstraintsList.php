<?php namespace Motorphp\SilexTools\ClassPattern\Constraints;

class ConstraintsList
{
    /** @var array|Constraint[] */
    private $constraints;

    public function allOf($from, array $to): ConstraintsList
    {
        $this->constraints[] = new All($from, $to);
        return $this;
    }

    public function oneOf($from, $to): ConstraintsList
    {
        $this->constraints[] = new One($from, $to);
        return $this;
    }

    public function noneOf($from, $to): ConstraintsList
    {
        $this->constraints[] = new None($from, $to);
        return $this;
    }

    public function addConstraint(Constraint $constraint): ConstraintsList
    {
        $this->constraints[] = $constraint;
        return $this;
    }

    /**
     * @return array|Constraint[]
     */
    public function toArray() : array
    {
        return $this->constraints;
    }
}