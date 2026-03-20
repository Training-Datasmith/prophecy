<?php

declare (strict_types=1);
namespace Prophecy\Doubler\Generator\Node\Type;

use Prophecy\Exception\Doubler\Double_Exception;
final readonly class Union_Type implements Type_Interface
{
    /**
     * @param list<SimpleType|IntersectionType> $types
     */
    public function __construct(private array $types)
    {
        $this->guard();
    }
    /**
     * @return list<SimpleType|IntersectionType>
     */
    public function get_types(): array
    {
        return $this->types;
    }
    private function guard(): void
    {
        $type_count = count($this->types);
        if ($type_count < 2) {
            // Throwing LogicException as this indicates misuse of the UnionType class itself.
            throw new Double_Exception(sprintf('UnionType must be constructed with at least two types. Got %d.', $type_count));
        }
        // To detect duplicates
        $type_strings = [];
        foreach ($this->types as $type) {
            if ($type instanceof Union_Type) {
                throw new Double_Exception('Union types cannot contain other unions.');
            }
            if ($type instanceof Intersection_Type) {
                $type_strings[] = implode('&', array_map(fn(Simple_Type $type): string => (string) $type, $type->get_types()));
                continue;
                // Valid type, nothing to be checked
            }
            if (!$type instanceof Simple_Type) {
                throw new Double_Exception(sprintf('Unexpected type "%s". Only IntersectionType and SimpleType are supported in UnionType.', $type::class));
            }
            $type_name = $type->get_type();
            $type_strings[] = $type_name;
            if (in_array($type_name, ['void', 'never', 'mixed'], true)) {
                throw new Double_Exception(sprintf('Type "%s" cannot be part of a union type.', $type_name));
            }
        }
        // Rule: Union types cannot contain duplicate types (e.g., int|string|int is invalid).
        // Reflection usually resolves this, but it's good practice to ensure consistency.
        if (count(array_unique($type_strings)) !== $type_count) {
            throw new Double_Exception(sprintf('Union types cannot contain duplicate types. Found duplicates in: %s', implode('|', $type_strings)));
        }
    }
    public function has(Simple_Type|Intersection_Type $given_type): bool
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
        if (!$given_type instanceof Union_Type) {
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
    public function __toString(): string
    {
        $result = '';
        foreach ($this->types as $type) {
            if ($result !== '') {
                $result .= '|';
            }
            if ($type instanceof Intersection_Type && count($this->types) > 1) {
                $result .= '(' . $type . ')';
                continue;
            }
            $result .= (string) $type;
        }
        return $result;
    }
}