<?php

declare (strict_types=1);
namespace Prophecy\Doubler\Generator\Node\Type;

final readonly class Object_Type implements Simple_Type
{
    /**
     * @param class-string $type
     */
    public function __construct(private string $type)
    {
    }
    public function __toString(): string
    {
        return '\\' . $this->type;
    }
    /**
     * @return class-string
     */
    public function get_type(): string
    {
        return $this->type;
    }
    public function equals(Type_Interface $given_type): bool
    {
        if (!$given_type instanceof Object_Type) {
            return false;
        }
        return $this->get_type() === $given_type->get_type();
    }
}