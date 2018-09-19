<?php namespace Motorphp\SilexTools\Matcher;

class TypeAcceptor
{
    /**
     * @var string
     */
    private $type;

    public static function acceptClass() : TypeAcceptor
    {
        return new TypeAcceptor(\ReflectionClass::class);
    }

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * @param \Reflector $reflector
     * @return bool
     */
    public function accepts(\Reflector $reflector): bool
    {
        if ($this->type === \ReflectionClass::class && $reflector instanceof \ReflectionClass) {
            return true;
        }

        if ($this->type === \ReflectionMethod::class && $reflector instanceof \ReflectionMethod) {
            return true;
        }

        if ($this->type === \ReflectionClassConstant::class && $reflector instanceof \ReflectionClassConstant) {
            return true;
        }

        if ($this->type === \ReflectionParameter::class && $reflector instanceof \ReflectionParameter) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}