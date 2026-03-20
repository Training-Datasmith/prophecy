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
use Prophecy\Doubler\Generator\Node\Method_Node;
use Prophecy\Doubler\Generator\Node\Return_Type_Node;
use Prophecy\Doubler\Generator\Node\Type\Builtin_Type;
/**
 * Traversable interface patch.
 * Forces classes that implement interfaces, that extend Traversable to also implement Iterator.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Traversable_Patch implements Class_Patch_Interface
{
    /**
     * Supports nodetree, that implement Traversable, but not Iterator or IteratorAggregate.
     *
     *
     */
    public function supports(Class_Node $node): bool
    {
        if (in_array('Iterator', $node->get_interfaces())) {
            return false;
        }
        if (in_array('IteratorAggregate', $node->get_interfaces())) {
            return false;
        }
        foreach ($node->get_interfaces() as $interface) {
            if ('Traversable' !== $interface && !is_subclass_of($interface, 'Traversable')) {
                continue;
            }
            if ('Iterator' === $interface) {
                continue;
            }
            if (is_subclass_of($interface, 'Iterator')) {
                continue;
            }
            if ('IteratorAggregate' === $interface) {
                continue;
            }
            if (is_subclass_of($interface, 'IteratorAggregate')) {
                continue;
            }
            return true;
        }
        return false;
    }
    /**
     * Forces class to implement Iterator interface.
     */
    public function apply(Class_Node $node): void
    {
        $node->add_interface('Iterator');
        $current_method = new Method_Node('current');
        $current_method->set_return_type_node(new Return_Type_Node(new Builtin_Type('mixed')));
        $node->add_method($current_method);
        $key_method = new Method_Node('key');
        $key_method->set_return_type_node(new Return_Type_Node(new Builtin_Type('mixed')));
        $node->add_method($key_method);
        $next_method = new Method_Node('next');
        $next_method->set_return_type_node(new Return_Type_Node(new Builtin_Type('void')));
        $node->add_method($next_method);
        $rewind_method = new Method_Node('rewind');
        $rewind_method->set_return_type_node(new Return_Type_Node(new Builtin_Type('void')));
        $node->add_method($rewind_method);
        $valid_method = new Method_Node('valid');
        $valid_method->set_return_type_node(new Return_Type_Node(new Builtin_Type('bool')));
        $node->add_method($valid_method);
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