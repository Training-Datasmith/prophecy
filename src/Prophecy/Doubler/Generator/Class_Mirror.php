<?php

declare (strict_types=1);
/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Prophecy\Doubler\Generator;

use Prophecy\Doubler\Generator\Node\Argument_Type_Node;
use Prophecy\Doubler\Generator\Node\Return_Type_Node;
use Prophecy\Doubler\Generator\Node\Type\Builtin_Type;
use Prophecy\Doubler\Generator\Node\Type\Intersection_Type;
use Prophecy\Doubler\Generator\Node\Type\Object_Type;
use Prophecy\Doubler\Generator\Node\Type\Simple_Type;
use Prophecy\Doubler\Generator\Node\Type\Type_Interface;
use Prophecy\Doubler\Generator\Node\Type\Union_Type;
use Prophecy\Exception\Doubler\Class_Mirror_Exception;
use Prophecy\Exception\InvalidArgumentException;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use Reflection_Type;
use ReflectionUnionType;
/**
 * Class mirror.
 * Core doubler class. Mirrors specific class and/or interfaces into class node tree.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Class_Mirror
{
    private const REFLECTABLE_METHODS = ['__construct', '__destruct', '__sleep', '__wakeup', '__toString', '__call', '__invoke'];
    /**
     * Reflects provided arguments into class node.
     *
     * @param ReflectionClass<object>|null $class
     * @param ReflectionClass<object>[]    $interfaces
     *
     *
     */
    public function reflect(?ReflectionClass $class, array $interfaces): \Prophecy\Doubler\Generator\Node\Class_Node
    {
        $node = new Node\Class_Node();
        if (null !== $class) {
            if (true === $class->is_interface()) {
                throw new InvalidArgumentException(sprintf("Could not reflect %s as a class, because it\n" . 'is interface - use the second argument instead.', $class->get_name()));
            }
            $this->reflect_class_to_node($class, $node);
        }
        foreach ($interfaces as $interface) {
            if (!$interface instanceof ReflectionClass) {
                throw new InvalidArgumentException(sprintf("[ReflectionClass \$interface1 [, ReflectionClass \$interface2]] array expected as\n" . 'a second argument to `ClassMirror::reflect(...)`, but got %s.', is_object($interface) ? $interface::class . ' class' : gettype($interface)));
            }
            if (false === $interface->is_interface()) {
                throw new InvalidArgumentException(sprintf("Could not reflect %s as an interface, because it\n" . 'is class - use the first argument instead.', $interface->get_name()));
            }
            $this->reflect_interface_to_node($interface, $node);
        }
        $node->add_interface(\Prophecy\Doubler\Generator\Reflection_Interface::class);
        return $node;
    }
    /**
     * @param ReflectionClass<object> $class
     */
    private function reflect_class_to_node(ReflectionClass $class, Node\Class_Node $node): void
    {
        if (true === $class->is_final()) {
            throw new Class_Mirror_Exception(sprintf('Could not reflect class %s as it is marked final.', $class->get_name()), $class);
        }
        if (method_exists(ReflectionClass::class, 'isReadOnly')) {
            $node->set_read_only($class->is_read_only());
        }
        $node->set_parent_class($class->get_name());
        foreach ($class->get_methods(ReflectionMethod::IS_ABSTRACT) as $method) {
            if (false === $method->is_protected()) {
                continue;
            }
            $this->reflect_method_to_node($method, $node);
        }
        foreach ($class->get_methods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (str_starts_with($method->get_name(), '_') && !in_array($method->get_name(), self::REFLECTABLE_METHODS)) {
                continue;
            }
            if (true === $method->is_final()) {
                $node->add_unextendable_method($method->get_name());
                continue;
            }
            $this->reflect_method_to_node($method, $node);
        }
    }
    /**
     * @param ReflectionClass<object> $interface
     */
    private function reflect_interface_to_node(ReflectionClass $interface, Node\Class_Node $node): void
    {
        $node->add_interface($interface->get_name());
        foreach ($interface->get_methods() as $method) {
            $this->reflect_method_to_node($method, $node);
        }
    }
    private function reflect_method_to_node(ReflectionMethod $method, Node\Class_Node $class_node): void
    {
        $node = new Node\Method_Node($method->get_name());
        if (true === $method->is_protected()) {
            $node->set_visibility('protected');
        }
        if (true === $method->is_static()) {
            $node->set_static();
        }
        if (true === $method->returns_reference()) {
            $node->set_returns_reference();
        }
        $return_reflection_type = null;
        if ($method->has_return_type()) {
            $return_reflection_type = $method->get_return_type();
        } elseif (method_exists($method, 'hasTentativeReturnType') && $method->has_tentative_return_type()) {
            // Tentative return types also need reflection
            $return_reflection_type = $method->get_tentative_return_type();
        }
        if (null !== $return_reflection_type) {
            $return_type = $this->create_type_from_reflection($return_reflection_type, $method->get_declaring_class());
            $node->set_return_type_node(new Return_Type_Node($return_type));
        }
        if (is_array($params = $method->get_parameters()) && count($params)) {
            foreach ($params as $param) {
                $this->reflect_argument_to_node($param, $method->get_declaring_class(), $node);
            }
        }
        $class_node->add_method($node);
    }
    /**
     * @param ReflectionClass<object> $declaringClass
     */
    private function reflect_argument_to_node(ReflectionParameter $parameter, ReflectionClass $declaring_class, Node\Method_Node $method_node): void
    {
        $name = $parameter->get_name() == '...' ? '__dot_dot_dot__' : $parameter->get_name();
        $node = new Node\Argument_Node($name);
        $ref_type = $parameter->get_type();
        if (null !== $ref_type) {
            $type_hint = $this->create_type_from_reflection($ref_type, $declaring_class);
            $node->set_type_node(new Argument_Type_Node($type_hint));
        }
        if ($parameter->is_variadic()) {
            $node->set_as_variadic();
        }
        if ($this->has_default_value($parameter)) {
            $node->set_default($this->get_default_value($parameter));
        }
        if ($parameter->is_passed_by_reference()) {
            $node->set_as_passed_by_reference();
        }
        $method_node->add_argument($node);
    }
    private function has_default_value(ReflectionParameter $parameter): bool
    {
        if ($parameter->is_variadic()) {
            return false;
        }
        if ($parameter->is_default_value_available()) {
            return true;
        }
        return $parameter->is_optional();
    }
    /**
     * @return mixed
     */
    private function get_default_value(ReflectionParameter $parameter)
    {
        if (!$parameter->is_default_value_available()) {
            return null;
        }
        return $parameter->get_default_value();
    }
    /**
     * @param ReflectionClass<object> $declaringClass Context reflection class
     */
    private function create_type_from_reflection(Reflection_Type $type, ReflectionClass $declaring_class): Type_Interface
    {
        if ($type instanceof ReflectionIntersectionType) {
            $inner_types = [];
            /** @var ReflectionNamedType $innerReflectionType */
            foreach ($type->get_types() as $inner_reflection_type) {
                // Intersections cannot be composed of builtin types
                /** @var class-string $objectType */
                $object_type = $inner_reflection_type->get_name();
                $inner_types[] = new Object_Type($object_type);
            }
            return new Intersection_Type($inner_types);
        }
        if ($type instanceof ReflectionUnionType) {
            $inner_types = [];
            /** @var ReflectionIntersectionType|ReflectionNamedType $innerReflectionType */
            foreach ($type->get_types() as $inner_reflection_type) {
                if ($inner_reflection_type instanceof ReflectionIntersectionType) {
                    /** @var IntersectionType $intersection */
                    $intersection = $this->create_type_from_reflection($inner_reflection_type, $declaring_class);
                    $inner_types[] = $intersection;
                    continue;
                }
                $name = $this->resolve_type_name($inner_reflection_type->get_name(), $declaring_class);
                if ($inner_reflection_type->is_builtin() || $name === 'static') {
                    $inner_types[] = new Builtin_Type($name);
                } elseif ($name === 'self') {
                    $inner_types[] = new Object_Type($declaring_class->get_name());
                } else {
                    /** @var class-string $name */
                    $inner_types[] = new Object_Type($name);
                }
            }
            // Nullability is handled by 'null' being one of the types in the union
            return new Union_Type($inner_types);
        }
        // Handle Named Types (single types like int, string, MyClass, ?MyClass)
        if ($type instanceof ReflectionNamedType) {
            $name = $this->resolve_type_name($type->get_name(), $declaring_class);
            if ($type->is_builtin() || $name === 'static') {
                $simple_type = new Builtin_Type($name);
                // SimpleType constructor normalizes
            } else {
                /** @var class-string $name */
                $simple_type = new Object_Type($name);
            }
            // Handle nullability for named types explicitly by wrapping in a UnionType if needed
            if ($type->allows_null() && $name !== 'mixed' && $name !== 'null') {
                return new Union_Type([new Builtin_Type('null'), $simple_type]);
            }
            return $simple_type;
        }
        // Unknown ReflectionType implementation
        throw new Class_Mirror_Exception('Unknown reflection type: ' . $type::class, $declaring_class);
    }
    /**
     * @param ReflectionClass<object> $contextClass
     */
    private function resolve_type_name(string $name, \ReflectionClass $context_class): string
    {
        if ($name === 'self') {
            return $context_class->get_name();
        }
        if ($name === 'parent') {
            $parent = $context_class->get_parent_class();
            if (false === $parent) {
                throw new Class_Mirror_Exception(sprintf('Cannot use "parent" type hint in class "%s" as it does not have a parent.', $context_class->get_name()), $context_class);
            }
            return $parent->get_name();
        }
        return $name;
    }
}