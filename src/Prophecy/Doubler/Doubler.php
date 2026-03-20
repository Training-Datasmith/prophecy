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

use Doctrine\Instantiator\Instantiator;
use Prophecy\Doubler\Class_Patch\Class_Patch_Interface;
use Prophecy\Doubler\Generator\Class_Creator;
use Prophecy\Doubler\Generator\Class_Mirror;
use Prophecy\Exception\InvalidArgumentException;
use ReflectionClass;
/**
 * Cached class doubler.
 * Prevents mirroring/creation of the same structure twice.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Doubler
{
    private readonly \Prophecy\Doubler\Generator\Class_Mirror $mirror;
    private readonly \Prophecy\Doubler\Generator\Class_Creator $creator;
    private readonly \Prophecy\Doubler\Name_Generator $namer;
    /**
     * @var list<ClassPatchInterface>
     */
    private array $patches = [];
    private ?\Doctrine\Instantiator\Instantiator $instantiator = null;
    public function __construct(?Class_Mirror $mirror = null, ?Class_Creator $creator = null, ?Name_Generator $namer = null)
    {
        $this->mirror = $mirror ?: new Class_Mirror();
        $this->creator = $creator ?: new Class_Creator();
        $this->namer = $namer ?: new Name_Generator();
    }
    /**
     * Returns list of registered class patches.
     *
     * @return list<ClassPatchInterface>
     */
    public function get_class_patches()
    {
        return $this->patches;
    }
    /**
     * Registers new class patch.
     *
     *
     */
    public function register_class_patch(Class_Patch_Interface $patch): void
    {
        $this->patches[] = $patch;
        @usort($this->patches, fn(Class_Patch_Interface $patch1, Class_Patch_Interface $patch2) => $patch2->get_priority() - $patch1->get_priority());
    }
    /**
     * Creates double from specific class or/and list of interfaces.
     *
     * @template T of object
     *
     * @param ReflectionClass<T>|null   $class
     * @param ReflectionClass<object>[] $interfaces Array of ReflectionClass instances
     * @param array<mixed>|null         $args       Constructor arguments
     *
     * @return T&DoubleInterface
     *
     * @throws \Prophecy\Exception\InvalidArgumentException
     */
    public function double(?ReflectionClass $class, array $interfaces, ?array $args = null)
    {
        foreach ($interfaces as $interface) {
            if (!$interface instanceof ReflectionClass) {
                throw new InvalidArgumentException(sprintf("[ReflectionClass \$interface1 [, ReflectionClass \$interface2]] array expected as\n" . 'a second argument to `Doubler::double(...)`, but got %s.', is_object($interface) ? $interface::class . ' class' : gettype($interface)));
            }
        }
        $classname = $this->create_double_class($class, $interfaces);
        $reflection = new ReflectionClass($classname);
        if (null !== $args) {
            return $reflection->new_instance_args($args);
        }
        if (null === ($constructor = $reflection->get_constructor()) || $constructor->is_public() && !$constructor->is_final()) {
            return $reflection->new_instance();
        }
        if (!$this->instantiator) {
            $this->instantiator = new Instantiator();
        }
        return $this->instantiator->instantiate($classname);
    }
    /**
     * Creates double class and returns its FQN.
     *
     * @template T of object
     *
     * @param ReflectionClass<T>|null   $class
     * @param ReflectionClass<object>[] $interfaces
     *
     * @return class-string<T&DoubleInterface>
     */
    protected function create_double_class(?ReflectionClass $class, array $interfaces): string
    {
        $name = $this->namer->name($class, $interfaces);
        $node = $this->mirror->reflect($class, $interfaces);
        foreach ($this->patches as $patch) {
            if ($patch->supports($node)) {
                $patch->apply($node);
            }
        }
        $node->add_interface(Double_Interface::class);
        $this->creator->create($name, $node);
        \assert(class_exists($name, false));
        return $name;
    }
}