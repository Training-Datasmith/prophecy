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

use Prophecy\Exception\Doubler\Method_Not_Extendable_Exception;
use Prophecy\Exception\InvalidArgumentException;
/**
 * Class node.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Class_Node
{
    /**
     * @var class-string
     */
    private $parent_class = 'stdClass';
    /**
     * @var list<class-string>
     */
    private array $interfaces = [];
    /**
     * @var array<string, string>
     *
     * @phpstan-var array<string, 'public'|'private'|'protected'>
     */
    private array $properties = [];
    /**
     * @var list<string>
     */
    private $unextendable_methods = [];
    /**
     * @var bool
     */
    private $read_only = false;
    /**
     * @var array<string, MethodNode>
     */
    private array $methods = [];
    /**
     * @return class-string
     */
    public function get_parent_class()
    {
        return $this->parent_class;
    }
    /**
     * @param class-string|null $class
     */
    public function set_parent_class($class): void
    {
        $this->parent_class = $class ?: 'stdClass';
    }
    /**
     * @return list<class-string>
     */
    public function get_interfaces()
    {
        return $this->interfaces;
    }
    /**
     * @param class-string $interface
     */
    public function add_interface($interface): void
    {
        if ($this->has_interface($interface)) {
            return;
        }
        array_unshift($this->interfaces, $interface);
    }
    /**
     * @param class-string $interface
     */
    public function has_interface($interface): bool
    {
        return in_array($interface, $this->interfaces);
    }
    /**
     * @return array<string, string>
     *
     * @phpstan-return array<string, 'public'|'private'|'protected'>
     */
    public function get_properties()
    {
        return $this->properties;
    }
    /**
     * @param string $visibility
     *
     *
     * @phpstan-param 'public'|'private'|'protected' $visibility
     */
    public function add_property(string $name, $visibility = 'public'): void
    {
        $visibility = strtolower($visibility);
        if (!\in_array($visibility, ['public', 'private', 'protected'], true)) {
            throw new InvalidArgumentException(sprintf('`%s` property visibility is not supported.', $visibility));
        }
        $this->properties[$name] = $visibility;
    }
    /**
     * @return array<string, MethodNode>
     */
    public function get_methods()
    {
        return $this->methods;
    }
    /**
     * @param bool       $force
     *
     */
    public function add_method(Method_Node $method, $force = false): void
    {
        if (!$this->is_extendable($method->get_name())) {
            $message = sprintf('Method `%s` is not extendable, so can not be added.', $method->get_name());
            throw new Method_Not_Extendable_Exception($message, $this->get_parent_class(), $method->get_name());
        }
        if ($force || !isset($this->methods[$method->get_name()])) {
            $this->methods[$method->get_name()] = $method;
        }
    }
    public function remove_method(string $name): void
    {
        unset($this->methods[$name]);
    }
    /**
     * @param string $name
     *
     * @return MethodNode|null
     */
    public function get_method($name)
    {
        return $this->has_method($name) ? $this->methods[$name] : null;
    }
    public function has_method(string $name): bool
    {
        return isset($this->methods[$name]);
    }
    /**
     * @return list<string>
     */
    public function get_unextendable_methods()
    {
        return $this->unextendable_methods;
    }
    /**
     * @param string $unextendableMethod
     */
    public function add_unextendable_method($unextendable_method): void
    {
        if (!$this->is_extendable($unextendable_method)) {
            return;
        }
        $this->unextendable_methods[] = $unextendable_method;
    }
    /**
     * @param string $method
     */
    public function is_extendable($method): bool
    {
        return !in_array($method, $this->unextendable_methods);
    }
    /**
     * @return bool
     */
    public function is_read_only()
    {
        return $this->read_only;
    }
    /**
     * @param bool $readOnly
     */
    public function set_read_only($read_only): void
    {
        $this->read_only = $read_only;
    }
}