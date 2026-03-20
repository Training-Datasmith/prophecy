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
namespace Prophecy\Doubler\Class_Patch;

use Prophecy\Doubler\Generator\Node\Class_Node;
/**
 * ReflectionClass::newInstance patch.
 * Makes first argument of newInstance optional, since it works but signature is misleading
 *
 * @author Florian Klein <florian.klein@free.fr>
 */
class Reflection_Class_New_Instance_Patch implements Class_Patch_Interface
{
    /**
     * Supports ReflectionClass
     *
     *
     */
    public function supports(Class_Node $node): bool
    {
        return 'ReflectionClass' === $node->get_parent_class();
    }
    /**
     * Updates newInstance's first argument to make it optional
     */
    public function apply(Class_Node $node): void
    {
        $method = $node->get_method('newInstance');
        \assert($method !== null);
        foreach ($method->get_arguments() as $argument) {
            $argument->set_default();
        }
    }
    /**
     * Returns patch priority, which determines when patch will be applied.
     *
     * @return int Priority number (higher = earlier)
     */
    public function get_priority(): int
    {
        return 50;
    }
}