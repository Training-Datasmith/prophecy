<?php

declare (strict_types=1);
namespace Prophecy\Doubler\Generator\Node\Type;

final readonly class Builtin_Type implements Simple_Type
{
    public function __construct(private string $type)
    {
    }
    public function __toString(): string
    {
        return $this->get_type();
    }
    public function get_type(): string
    {
        return $this->type;
    }
    public function equals(Type_Interface $given_type): bool
    {
        if (!$given_type instanceof Builtin_Type) {
            return false;
        }
        return $this->get_type() === $given_type->get_type();
    }
}