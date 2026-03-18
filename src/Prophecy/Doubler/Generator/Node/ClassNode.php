<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prophecy\Doubler\Generator\Node;

use Prophecy\Exception\Doubler\MethodNotExtendableException;
use Prophecy\Exception\InvalidArgumentException;

/**
 * Class node.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class ClassNode
{
    /**
     * @var class-string
     */
    private $parentClass = 'stdClass';
    /**
     * @var list<class-string>
     */
    private array $interfaces  = [];

    /**
     * @var array<string, string>
     *
     * @phpstan-var array<string, 'public'|'private'|'protected'>
     */
    private array $properties  = [];

    /**
     * @var list<string>
     */
    private $unextendableMethods = [];

    /**
     * @var bool
     */
    private $readOnly = false;

    /**
     * @var array<string, MethodNode>
     */
    private array $methods = [];

    /**
     * @return class-string
     */
    public function getParentClass()
    {
        return $this->parentClass;
    }

    /**
     * @param class-string|null $class
     */
    public function setParentClass($class): void
    {
        $this->parentClass = $class ?: 'stdClass';
    }

    /**
     * @return list<class-string>
     */
    public function getInterfaces()
    {
        return $this->interfaces;
    }

    /**
     * @param class-string $interface
     */
    public function addInterface($interface): void
    {
        if ($this->hasInterface($interface)) {
            return;
        }

        array_unshift($this->interfaces, $interface);
    }

    /**
     * @param class-string $interface
     */
    public function hasInterface($interface): bool
    {
        return in_array($interface, $this->interfaces);
    }

    /**
     * @return array<string, string>
     *
     * @phpstan-return array<string, 'public'|'private'|'protected'>
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param string $visibility
     *
     *
     * @phpstan-param 'public'|'private'|'protected' $visibility
     */
    public function addProperty(string $name, $visibility = 'public'): void
    {
        $visibility = strtolower($visibility);

        if (!\in_array($visibility, ['public', 'private', 'protected'], true)) {
            throw new InvalidArgumentException(sprintf(
                '`%s` property visibility is not supported.', $visibility
            ));
        }

        $this->properties[$name] = $visibility;
    }

    /**
     * @return array<string, MethodNode>
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @param bool       $force
     *
     */
    public function addMethod(MethodNode $method, $force = false): void
    {
        if (!$this->isExtendable($method->getName())) {
            $message = sprintf(
                'Method `%s` is not extendable, so can not be added.', $method->getName()
            );
            throw new MethodNotExtendableException($message, $this->getParentClass(), $method->getName());
        }

        if ($force || !isset($this->methods[$method->getName()])) {
            $this->methods[$method->getName()] = $method;
        }
    }

    public function removeMethod(string $name): void
    {
        unset($this->methods[$name]);
    }

    /**
     * @param string $name
     *
     * @return MethodNode|null
     */
    public function getMethod($name)
    {
        return $this->hasMethod($name) ? $this->methods[$name] : null;
    }

    public function hasMethod(string $name): bool
    {
        return isset($this->methods[$name]);
    }

    /**
     * @return list<string>
     */
    public function getUnextendableMethods()
    {
        return $this->unextendableMethods;
    }

    /**
     * @param string $unextendableMethod
     */
    public function addUnextendableMethod($unextendableMethod): void
    {
        if (!$this->isExtendable($unextendableMethod)) {
            return;
        }
        $this->unextendableMethods[] = $unextendableMethod;
    }

    /**
     * @param string $method
     */
    public function isExtendable($method): bool
    {
        return !in_array($method, $this->unextendableMethods);
    }

    /**
     * @return bool
     */
    public function isReadOnly()
    {
        return $this->readOnly;
    }

    /**
     * @param bool $readOnly
     */
    public function setReadOnly($readOnly): void
    {
        $this->readOnly = $readOnly;
    }
}
