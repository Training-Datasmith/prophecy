<?php

declare (strict_types=1);
namespace Prophecy\Doubler\Class_Patch;

use Prophecy\Doubler\Generator\Node\Class_Node;
use Prophecy\Exception\Doubler\Class_Creator_Exception;
class Throwable_Patch implements Class_Patch_Interface
{
    /**
     * Checks if patch supports specific class node.
     */
    public function supports(Class_Node $node): bool
    {
        return $this->implements_a_throwable_interface($node) && $this->does_not_extend_a_throwable_class($node);
    }
    private function implements_a_throwable_interface(Class_Node $node): bool
    {
        foreach ($node->get_interfaces() as $type) {
            if (is_a($type, 'Throwable', true)) {
                return true;
            }
        }
        return false;
    }
    private function does_not_extend_a_throwable_class(Class_Node $node): bool
    {
        return !is_a($node->get_parent_class(), 'Throwable', true);
    }
    /**
     * Applies patch to the specific class node.
     *
     *
     */
    public function apply(Class_Node $node): void
    {
        $this->check_it_can_be_doubled($node);
        $this->set_parent_class_to_exception($node);
    }
    private function check_it_can_be_doubled(Class_Node $node): void
    {
        $class_name = $node->get_parent_class();
        if ($class_name !== 'stdClass') {
            throw new Class_Creator_Exception(sprintf('Cannot double concrete class %s as well as implement Traversable', $class_name), $node);
        }
    }
    private function set_parent_class_to_exception(Class_Node $node): void
    {
        $node->set_parent_class('Exception');
        $node->remove_method('getMessage');
        $node->remove_method('getCode');
        $node->remove_method('getFile');
        $node->remove_method('getLine');
        $node->remove_method('getTrace');
        $node->remove_method('getPrevious');
        $node->remove_method('getNext');
        $node->remove_method('getTraceAsString');
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