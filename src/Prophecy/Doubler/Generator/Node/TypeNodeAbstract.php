<?php

declare (strict_types=1);
namespace Prophecy\Doubler\Generator\Node;

use Prophecy\Doubler\Generator\Node\Type\Builtin_Type;
use Prophecy\Doubler\Generator\Node\Type\Intersection_Type;
use Prophecy\Doubler\Generator\Node\Type\Object_Type;
use Prophecy\Doubler\Generator\Node\Type\Simple_Type;
use Prophecy\Doubler\Generator\Node\Type\Type_Interface;
use Prophecy\Doubler\Generator\Node\Type\Union_Type;
use Prophecy\Exception\Doubler\Double_Exception;
abstract class Type_Node_Abstract
{
    // null means no type, NOT BuiltInType("null")
    private ?Type_Interface $type;
    public function __construct(string|Type_Interface|null $type = null, string ...$types)
    {
        if (!empty($types) || is_string($type)) {
            $types = [$type, ...$types];
        }
        if (!empty($types)) {
            // BC Layer for usage with strings
            trigger_deprecation('phpspec/prophecy', '1.23', 'Instanciating node type with a string type will not be supported in the future, use a TypeInterface instance instead.');
            // BC Layer for usage with strings
            $types_normalized = [];
            /** @var list<BuiltinType|ObjectType|IntersectionType> $union */
            $union = [];
            foreach ($types as $type) {
                if (!is_string($type)) {
                    throw new Double_Exception('Building a TypeNode with string is deprecated. Mixing strings and type object is not allowed.');
                }
                if ($this->is_built_in($type)) {
                    $type = new Builtin_Type($this->normalize_builtin_type($type));
                } else {
                    /** @var class-string $typeName */
                    $type_name = $this->remove_prefix_ns_separator($type);
                    $type = new Object_Type($type_name);
                }
                if (!in_array($type->get_type(), $types_normalized, true)) {
                    $union[] = $type;
                    $types_normalized[] = $type->get_type();
                }
            }
            if (count($union) > 1) {
                $this->type = new Union_Type($union);
            } else {
                $this->type = $union[0];
            }
        } else {
            /** @var TypeInterface|null $type */
            $this->type = $type;
        }
    }
    /**
     * @deprecated use isNullable() instead
     */
    public function can_use_null_shorthand(): bool
    {
        trigger_deprecation('phpspec/prophecy', '1.23', 'This method is deprecated in favor of nullable()');
        if ($this->type instanceof Union_Type) {
            return $this->type->has(new Builtin_Type('null')) && count($this->type->get_types()) === 2;
        }
        return false;
    }
    public function is_nullable(): bool
    {
        if ($this->type instanceof Union_Type) {
            return $this->type->has(new Builtin_Type('null'));
        }
        if ($this->type instanceof Simple_Type && $this->type->get_type() === 'null') {
            return true;
        }
        return false;
    }
    /**
     * @return list<string>
     * @deprecated use getType() instead
     */
    public function get_types(): array
    {
        trigger_deprecation('phpspec/prophecy', '1.23', 'This method is deprecated in favor of getType()');
        if ($this->type instanceof Simple_Type) {
            return [(string) $this->type];
        }
        $types = [];
        if ($this->type instanceof Union_Type) {
            foreach ($this->type->get_types() as $type) {
                if ($type instanceof Intersection_Type) {
                    throw new Double_Exception('getType() method is deprecated and do not support IntersectionType by design. Use getType() instead.');
                }
                $types[$type->get_type()] = (string) $type;
            }
        }
        $types = array_values($types);
        $types = array_map($this->normalize_builtin_type(...), $types);
        return array_values(array_unique($types));
    }
    public function get_type(): ?Type_Interface
    {
        return $this->type;
    }
    /**
     * @deprecated use getType() instead
     * @return list<string>
     */
    public function get_non_null_types(): array
    {
        trigger_deprecation('phpspec/prophecy', '1.23', 'This method is deprecated in favor of getType() and the usage of the new type API.');
        if ($this->type === null) {
            return [];
        }
        if ($this->type instanceof Union_Type) {
            $types = [];
            foreach ($this->type->get_types() as $type) {
                if ($type instanceof Intersection_Type) {
                    throw new Double_Exception('You are using the old (and deprecated) API which is not compatible with intersections');
                }
                if (!$type instanceof Builtin_Type || $type->get_type() !== 'null') {
                    $types[] = $type->get_type();
                }
            }
            return $types;
        }
        if ($this->type instanceof Simple_Type) {
            if ($this->type->get_type() === 'null') {
                return [];
            }
            return [$this->type->get_type()];
        }
        throw new Double_Exception('getNonNullTypes() method is deprecated and do not support IntersectionType by design. Use getType() instead.');
    }
    private function normalize_builtin_type(string $type): string
    {
        return match ($type) {
            'double', 'real' => 'float',
            'integer' => 'int',
            'boolean' => 'bool',
            default => $type,
        };
    }
    protected function is_built_in(string $type): bool
    {
        return match ($type) {
            'double', 'real', 'boolean', 'integer', 'self', 'static', 'array', 'callable', 'bool', 'false', 'true', 'float', 'int', 'string', 'iterable', 'object', 'null', 'mixed', 'void', 'never' => true,
            // Class / Interface type
            default => false,
        };
    }
    protected function remove_prefix_ns_separator(string $type): string
    {
        return ltrim($type, '\\');
    }
}