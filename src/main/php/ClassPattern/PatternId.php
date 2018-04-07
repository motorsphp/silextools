<?php namespace Motorphp\SilexTools\ClassPattern;

class PatternId
{
    private static $lastId = 0;

    /**
     * @param string $matchKey
     * @return PatternId
     */
    public static function next(string $matchKey) : PatternId
    {
        PatternId::$lastId++;
        return new PatternId(PatternId::$lastId, $matchKey);
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

    /**
     * @var string
     */
    private $matchKey;

    public function __construct(string $id, string $matchKey)
    {
        $this->id = $id;
        $this->matchKey = $matchKey;
    }

    public function getMatchKey(): string
    {
        return $this->matchKey;
    }

    public function equals(PatternId $other)
    {
        return $this->id === $other->toString() ;
    }

    public function toString()
    {
        return (string) $this->id;
    }

}