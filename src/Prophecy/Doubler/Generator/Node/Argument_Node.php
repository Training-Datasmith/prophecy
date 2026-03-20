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
namespace Prophecy\Doubler\Generator\Node;

/**
 * Argument node.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Argument_Node
{
    /**
     * @var mixed
     */
    private $default;
    private bool $optional = false;
    /**
     * @var bool
     */
    private $by_reference = false;
    /**
     * @var bool
     */
    private $is_variadic = false;
    private \Prophecy\Doubler\Generator\Node\Argument_Type_Node $type_node;
    /**
     * @param string $name
     */
    public function __construct(private $name)
    {
        $this->type_node = new Argument_Type_Node();
    }
    /**
     * @return string
     */
    public function get_name()
    {
        return $this->name;
    }
    public function set_type_node(Argument_Type_Node $type_node): void
    {
        $this->type_node = $type_node;
    }
    public function get_type_node(): Argument_Type_Node
    {
        return $this->type_node;
    }
    public function has_default(): bool
    {
        return $this->is_optional() && !$this->is_variadic();
    }
    /**
     * @return mixed
     */
    public function get_default()
    {
        return $this->default;
    }
    /**
     * @param mixed $default
     */
    public function set_default($default = null): void
    {
        $this->optional = true;
        $this->default = $default;
    }
    /**
     * @return bool
     */
    public function is_optional()
    {
        return $this->optional;
    }
    /**
     * @param bool $byReference
     */
    public function set_as_passed_by_reference($by_reference = true): void
    {
        $this->by_reference = $by_reference;
    }
    /**
     * @return bool
     */
    public function is_passed_by_reference()
    {
        return $this->by_reference;
    }
    /**
     * @param bool $isVariadic
     */
    public function set_as_variadic($is_variadic = true): void
    {
        $this->is_variadic = $is_variadic;
    }
    /**
     * @return bool
     */
    public function is_variadic()
    {
        return $this->is_variadic;
    }
    /**
     * @deprecated use getArgumentTypeNode instead
     */
    public function get_type_hint(): ?string
    {
        $type = $this->type_node->get_non_null_types() ? $this->type_node->get_non_null_types()[0] : null;
        return $type ? ltrim($type, '\\') : null;
    }
    /**
     * @deprecated use setArgumentTypeNode instead
     * @param string|null $typeHint
     */
    public function set_type_hint($type_hint = null): void
    {
        $this->type_node = $type_hint === null ? new Argument_Type_Node() : new Argument_Type_Node($type_hint);
    }
    /**
     * @deprecated use getArgumentTypeNode instead
     */
    public function is_nullable(): bool
    {
        return $this->type_node->can_use_null_shorthand();
    }
    /**
     * @deprecated use getArgumentTypeNode instead
     * @param bool $isNullable
     */
    public function set_as_nullable($is_nullable = true): void
    {
        $non_null_types = $this->type_node->get_non_null_types();
        $this->type_node = $is_nullable ? new Argument_Type_Node('null', ...$non_null_types) : new Argument_Type_Node(...$non_null_types);
    }
}