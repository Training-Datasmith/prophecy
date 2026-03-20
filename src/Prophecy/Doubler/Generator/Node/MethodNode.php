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

use Prophecy\Exception\InvalidArgumentException;
/**
 * Method node.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Method_Node
{
    /**
     * @phpstan-var 'public'|'private'|'protected'
     */
    private string $visibility = 'public';
    private bool $static = false;
    private bool $returns_reference = false;
    private \Prophecy\Doubler\Generator\Node\Return_Type_Node $return_type_node;
    /**
     * @var list<ArgumentNode>
     */
    private array $arguments = [];
    // Used to accept an optional third argument with the deprecated Prophecy\Doubler\Generator\TypeHintReference so careful when adding a new argument in a minor version.
    /**
     * @param string      $name
     * @param string|null $code
     */
    public function __construct(private $name, private $code = null)
    {
        $this->return_type_node = new Return_Type_Node();
    }
    /**
     * @return string
     *
     * @phpstan-return 'public'|'private'|'protected'
     */
    public function get_visibility()
    {
        return $this->visibility;
    }
    /**
     * @param string $visibility
     */
    public function set_visibility($visibility): void
    {
        $visibility = strtolower($visibility);
        if (!\in_array($visibility, ['public', 'private', 'protected'], true)) {
            throw new InvalidArgumentException(sprintf('`%s` method visibility is not supported.', $visibility));
        }
        $this->visibility = $visibility;
    }
    /**
     * @return bool
     */
    public function is_static()
    {
        return $this->static;
    }
    /**
     * @param bool $static
     */
    public function set_static($static = true): void
    {
        $this->static = (bool) $static;
    }
    /**
     * @return bool
     */
    public function returns_reference()
    {
        return $this->returns_reference;
    }
    public function set_returns_reference(): void
    {
        $this->returns_reference = true;
    }
    /**
     * @return string
     */
    public function get_name()
    {
        return $this->name;
    }
    public function add_argument(Argument_Node $argument): void
    {
        $this->arguments[] = $argument;
    }
    /**
     * @return list<ArgumentNode>
     */
    public function get_arguments()
    {
        return $this->arguments;
    }
    /**
     * @deprecated use getReturnTypeNode instead
     */
    public function has_return_type(): bool
    {
        return (bool) $this->return_type_node->get_non_null_types();
    }
    public function set_return_type_node(Return_Type_Node $return_type_node): void
    {
        $this->return_type_node = $return_type_node;
    }
    /**
     * @deprecated use setReturnTypeNode instead
     * @param string $type
     */
    public function set_return_type($type = null): void
    {
        $this->return_type_node = $type === '' || $type === null ? new Return_Type_Node() : new Return_Type_Node($type);
    }
    /**
     * @deprecated use setReturnTypeNode instead
     * @param bool $bool
     */
    public function set_nullable_return_type($bool = true): void
    {
        if ($bool) {
            $this->return_type_node = new Return_Type_Node('null', ...$this->return_type_node->get_types());
        } else {
            $this->return_type_node = new Return_Type_Node(...$this->return_type_node->get_non_null_types());
        }
    }
    /**
     * @deprecated use getReturnTypeNode instead
     * @return string|null
     */
    public function get_return_type()
    {
        if ($types = $this->return_type_node->get_non_null_types()) {
            return $types[0];
        }
        return null;
    }
    public function get_return_type_node(): Return_Type_Node
    {
        return $this->return_type_node;
    }
    /**
     * @deprecated use getReturnTypeNode instead
     */
    public function has_nullable_return_type(): bool
    {
        return $this->return_type_node->is_nullable();
    }
    /**
     * @param string $code
     */
    public function set_code($code): void
    {
        $this->code = $code;
    }
    public function get_code(): string
    {
        if ($this->returns_reference) {
            return "throw new \\Prophecy\\Exception\\Doubler\\ReturnByReferenceException('Returning by reference not supported', get_class(\$this), '{$this->name}');";
        }
        return (string) $this->code;
    }
    public function use_parent_code(): void
    {
        $this->code = sprintf('return parent::%s(%s);', $this->get_name(), implode(', ', array_map($this->generate_argument(...), $this->arguments)));
    }
    private function generate_argument(Argument_Node $arg): string
    {
        $argument = '$' . $arg->get_name();
        if ($arg->is_variadic()) {
            return '...' . $argument;
        }
        return $argument;
    }
}