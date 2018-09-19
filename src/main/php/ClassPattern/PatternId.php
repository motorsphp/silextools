<?php namespace Motorphp\SilexTools\ClassPattern;

class PatternId
{
    private static $lastId = 0;

    /**
     * @return PatternId
     */
    public static function next() : PatternId
    {
        PatternId::$lastId++;
        return new PatternId(PatternId::$lastId);
    }

    /**
     * @param PatternId $id
     * @param array|PatternId[] $ids
     * @return bool
     */
    public static function inArray(PatternId $id, array $ids) : bool
    {
        foreach ($ids as $otherIds) {
            if ($id->equals($otherIds)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @var string
     */
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function equals(PatternId $other)
    {
        return $this->id === $other->asString() ;
    }

    public function asString() : string
    {
        return (string) $this->id;
    }

}