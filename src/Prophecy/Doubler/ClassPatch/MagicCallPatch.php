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
use Prophecy\Doubler\Generator\Node\Class_Node;
use Prophecy\Doubler\Generator\Node\Method_Node;
use Prophecy\Php_Documentor\Class_And_Interface_Tag_Retriever;
use Prophecy\Php_Documentor\Method_Tag_Retriever_Interface;
/**
 * Discover Magical API using "@method" PHPDoc format.
 *
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 * @author Kévin Dunglas <dunglas@gmail.com>
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class Magic_Call_Patch implements Class_Patch_Interface
{
    public const MAGIC_METHODS_WITH_ARGUMENTS = ['__call', '__callStatic', '__get', '__isset', '__set', '__set_state', '__unserialize', '__unset'];
    public function __construct(private readonly ?Method_Tag_Retriever_Interface $tag_retriever = new Class_And_Interface_Tag_Retriever())
    {
    }
    /**
     * Support any class
     *
     *
     */
    public function supports(Class_Node $node): bool
    {
        return true;
    }
    /**
     * Discover Magical API
     */
    public function apply(Class_Node $node): void
    {
        $types = array_filter($node->get_interfaces(), fn(string $interface) => !str_starts_with($interface, 'Prophecy\\'));
        $types[] = $node->get_parent_class();
        foreach ($types as $type) {
            $reflection_class = new \ReflectionClass($type);
            while ($reflection_class) {
                $tag_list = $this->tag_retriever->get_tag_list($reflection_class);
                foreach ($tag_list as $tag) {
                    $method_name = $tag->get_method_name();
                    if (empty($method_name)) {
                        continue;
                    }
                    if (!$reflection_class->has_method($method_name)) {
                        $method_node = new Method_Node($method_name);
                        // only magic methods can have a contract that needs to be enforced
                        if (in_array($method_name, self::MAGIC_METHODS_WITH_ARGUMENTS)) {
                            if (method_exists($tag, 'getParameters')) {
                                // Reflection Docblock 5.4.0+.
                                foreach ($tag->get_parameters() as $argument) {
                                    $argument_node = new Argument_Node($argument->get_name());
                                    $method_node->add_argument($argument_node);
                                }
                            } else {
                                // Reflection Docblock < 5.4.0.
                                foreach ($tag->get_arguments() as $argument) {
                                    $argument_node = new Argument_Node($argument['name']);
                                    $method_node->add_argument($argument_node);
                                }
                            }
                        }
                        $method_node->set_static($tag->is_static());
                        $node->add_method($method_node);
                    }
                }
                $reflection_class = $reflection_class->get_parent_class();
            }
        }
    }
    /**
     * Returns patch priority, which determines when patch will be applied.
     *
     * @return integer Priority number (higher - earlier)
     */
    public function get_priority(): int
    {
        return 50;
    }
}