<?php

declare (strict_types=1);
namespace Prophecy\Doubler\Generator\Node\Type;

use Prophecy\Exception\Doubler\Double_Exception;
final readonly class Intersection_Type implements Type_Interface
{
    /**
     * @param list<ObjectType> $types
     */
    public function __construct(private array $types)
    {
        $this->guard();
    }
    /**
     * @return list<ObjectType>
     */
    public function get_types(): array
    {
        return $this->types;
    }
    private function has(Simple_Type $given_type): bool
    {
        foreach ($this->types as $type) {
            if ($type->equals($given_type)) {
                return true;
            }
        }
        return false;
    }
    public function equals(Type_Interface $given_type): bool
    {
        if (!$given_type instanceof Intersection_Type) {
            return false;
        }
        if (count($this->types) !== count($given_type->get_types())) {
            return false;
        }
        foreach ($this->types as $type) {
            if (!$given_type->has($type)) {
                return false;
            }
        }
        return true;
    }
    private function guard(): void
    {
        // Cannot contain void, never, null, scalar types, mixed, union types etc.
        foreach ($this->types as $type) {
            if (!$type instanceof Object_Type) {
                throw new Double_Exception('Intersection types can only contain class/interface names.');
            }
        }
        if (count($this->types) < 2) {
            throw new Double_Exception('Intersection types must contain at least two types.');
        }
    }
    public function __toString(): string
    {
        $result = '';
        foreach ($this->types as $type) {
            if ($result !== '') {
                $result .= '&';
            }
            $result .= (string) $type;
        }
        return $result;
    }
}