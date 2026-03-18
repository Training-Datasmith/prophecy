<?php

declare(strict_types=1);

namespace Prophecy\Doubler\Generator\Node\Type;

final readonly class ObjectType implements SimpleType
{
    /**
     * @param class-string $type
     */
    public function __construct(private string $type)
    {
    }

    public function __toString(): string
    {
        return '\\'.$this->type;
    }

    /**
     * @return class-string
     */
    public function getType(): string
    {
        return $this->type;
    }

    public function equals(TypeInterface $givenType): bool
    {
        if (!$givenType instanceof ObjectType) {
            return false;
        }

        return $this->getType() === $givenType->getType();
    }
}
