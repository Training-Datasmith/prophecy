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

use Prophecy\Doubler\Generator\Node\Argument_Node;
use Prophecy\Doubler\Generator\Node\Argument_Type_Node;
use Prophecy\Doubler\Generator\Node\Class_Node;
use Prophecy\Doubler\Generator\Node\Method_Node;
use Prophecy\Doubler\Generator\Node\Type\Object_Type;
/**
 * Add Prophecy functionality to the double.
 * This is a core class patch for Prophecy.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Prophecy_Subject_Patch implements Class_Patch_Interface
{
    /**
     * Always returns true.
     *
     *
     */
    public function supports(Class_Node $node): bool
    {
        return true;
    }
    /**
     * Apply Prophecy functionality to class node.
     */
    public function apply(Class_Node $node): void
    {
        $node->add_interface(\Prophecy\Prophecy\Prophecy_Subject_Interface::class);
        $node->add_property('objectProphecyClosure', 'private');
        foreach ($node->get_methods() as $name => $method) {
            if ('__construct' === strtolower($name)) {
                continue;
            }
            if (!$method->get_return_type_node()->has_return_statement()) {
                $method->set_code('$this->getProphecy()->makeProphecyMethodCall(__FUNCTION__, func_get_args());');
            } else {
                $method->set_code('return $this->getProphecy()->makeProphecyMethodCall(__FUNCTION__, func_get_args());');
            }
        }
        $prophecy_setter = new Method_Node('setProphecy');
        $prophecy_argument = new Argument_Node('prophecy');
        $prophecy_argument->set_type_node(new Argument_Type_Node(new Object_Type(\Prophecy\Prophecy\Prophecy_Interface::class)));
        $prophecy_setter->add_argument($prophecy_argument);
        $prophecy_setter->set_code(<<<PHP
        if (null === \$this->objectProphecyClosure) {
            \$this->objectProphecyClosure = static function () use (\$prophecy) {
                return \$prophecy;
            };
        }
        PHP);
        $prophecy_getter = new Method_Node('getProphecy');
        $prophecy_getter->set_code('return \call_user_func($this->objectProphecyClosure);');
        if ($node->has_method('__call')) {
            $__call = $node->get_method('__call');
            \assert($__call !== null);
        } else {
            $__call = new Method_Node('__call');
            $__call->add_argument(new Argument_Node('name'));
            $__call->add_argument(new Argument_Node('arguments'));
            $node->add_method($__call, true);
        }
        $__call->set_code(<<<PHP
        throw new \\Prophecy\\Exception\\Doubler\\MethodNotFoundException(
            sprintf('Method `%s::%s()` not found.', get_class(\$this), func_get_arg(0)),
            get_class(\$this), func_get_arg(0)
        );
        PHP);
        $node->add_method($prophecy_setter, true);
        $node->add_method($prophecy_getter, true);
    }
    /**
     * Returns patch priority, which determines when patch will be applied.
     *
     * @return int Priority number (higher - earlier)
     */
    public function get_priority(): int
    {
        return 0;
    }
}