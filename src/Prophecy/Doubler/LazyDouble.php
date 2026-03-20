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
namespace Prophecy\Doubler;

use Prophecy\Exception\Doubler\Class_Not_Found_Exception;
use Prophecy\Exception\Doubler\Double_Exception;
use Prophecy\Exception\Doubler\Interface_Not_Found_Exception;
use ReflectionClass;
/**
 * Lazy double.
 * Gives simple interface to describe double before creating it.
 *
 * @template T of object
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Lazy_Double
{
    /**
     * @var ReflectionClass<T>|null
     */
    private ?\ReflectionClass $class = null;
    /**
     * @var list<ReflectionClass<object>>
     */
    private array $interfaces = [];
    /**
     * @var array<mixed>|null
     */
    private ?array $arguments = null;
    /**
     * @var (T&DoubleInterface)|null
     */
    private $double;
    public function __construct(private readonly Doubler $doubler)
    {
    }
    /**
     * Tells doubler to use specific class as parent one for double.
     *
     * @param class-string|ReflectionClass<object> $class
     *
     *
     * @template U of object
     * @phpstan-param class-string<U>|ReflectionClass<U> $class
     * @phpstan-this-out static<U>
     *
     * @throws ClassNotFoundException
     * @throws DoubleException
     */
    public function set_parent_class($class): void
    {
        if (null !== $this->double) {
            throw new Double_Exception('Can not extend class with already instantiated double.');
        }
        if (!$class instanceof ReflectionClass) {
            if (!class_exists($class)) {
                throw new Class_Not_Found_Exception(sprintf('Class %s not found.', $class), $class);
            }
            $class = new ReflectionClass($class);
        }
        /** @var static<U> $this */
        $this->class = $class;
    }
    /**
     * Tells doubler to implement specific interface with double.
     *
     * @param class-string|ReflectionClass<object> $interface
     *
     *
     * @template U of object
     * @phpstan-param class-string<U>|ReflectionClass<U> $interface
     * @phpstan-this-out static<T&U>
     *
     * @throws InterfaceNotFoundException
     * @throws DoubleException
     */
    public function add_interface($interface): void
    {
        if (null !== $this->double) {
            throw new Double_Exception('Can not implement interface with already instantiated double.');
        }
        if (!$interface instanceof ReflectionClass) {
            if (!interface_exists($interface)) {
                throw new Interface_Not_Found_Exception(sprintf('Interface %s not found.', $interface), $interface);
            }
            $interface = new ReflectionClass($interface);
        }
        $this->interfaces[] = $interface;
    }
    /**
     * Sets constructor arguments.
     *
     * @param array<mixed>|null $arguments
     */
    public function set_arguments(?array $arguments = null): void
    {
        $this->arguments = $arguments;
    }
    /**
     * Creates double instance or returns already created one.
     *
     * @return T&DoubleInterface
     */
    public function get_instance()
    {
        if (null === $this->double) {
            if (null !== $this->arguments) {
                return $this->double = $this->doubler->double($this->class, $this->interfaces, $this->arguments);
            }
            $this->double = $this->doubler->double($this->class, $this->interfaces);
        }
        return $this->double;
    }
}