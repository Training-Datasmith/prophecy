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

use Prophecy\Doubler\Generator\Node\Argument_Type_Node;
use Prophecy\Doubler\Generator\Node\Class_Node;
use Prophecy\Doubler\Generator\Node\Method_Node;
use Prophecy\Doubler\Generator\Node\Type\Builtin_Type;
use Prophecy\Doubler\Generator\Node\Type\Union_Type;
/**
 * Disable constructor.
 * Makes all constructor arguments optional.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Disable_Constructor_Patch implements Class_Patch_Interface
{
    /**
     * Checks if class has `__construct` method.
     *
     *
     */
    public function supports(Class_Node $node): bool
    {
        return true;
    }
    /**
     * Makes all class constructor arguments optional.
     */
    public function apply(Class_Node $node): void
    {
        if (!$node->is_extendable('__construct')) {
            return;
        }
        if (!$node->has_method('__construct')) {
            $node->add_method(new Method_Node('__construct', ''));
            return;
        }
        $constructor = $node->get_method('__construct');
        \assert($constructor !== null);
        foreach ($constructor->get_arguments() as $argument) {
            $argument->set_default();
            $type = $argument->get_type_node()->get_type();
            if ($type instanceof Builtin_Type && ($type->get_type() === 'null' || $type->get_type() === 'mixed')) {
                continue;
            }
            if ($type instanceof Union_Type && $type->has(new Builtin_Type('null'))) {
                continue;
            }
            if (null === $type) {
                continue;
            }
            if ($type instanceof Union_Type) {
                $argument->set_type_node(new Argument_Type_Node(new Union_Type([new Builtin_Type('null'), ...$type->get_types()])));
                continue;
            }
            $argument->set_type_node(new Argument_Type_Node(new Union_Type([new Builtin_Type('null'), $type])));
        }
        $constructor->set_code(<<<PHP
        if (0 < func_num_args()) {
            call_user_func_array(array(parent::class, '__construct'), func_get_args());
        }
        PHP);
    }
    /**
     * Returns patch priority, which determines when patch will be applied.
     *
     * @return int Priority number (higher - earlier)
     */
    public function get_priority(): int
    {
        return 100;
    }
}