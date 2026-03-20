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
namespace Prophecy\Php_Documentor;

use Php_Documentor\Reflection\Doc_Block\Tags\Method;
/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 *
 * @internal
 */
final class Class_And_Interface_Tag_Retriever implements Method_Tag_Retriever_Interface
{
    private \Prophecy\Php_Documentor\Method_Tag_Retriever_Interface|\Prophecy\Php_Documentor\Class_Tag_Retriever $class_retriever;
    public function __construct(?Method_Tag_Retriever_Interface $class_retriever = null)
    {
        if (null !== $class_retriever) {
            $this->class_retriever = $class_retriever;
            return;
        }
        $this->class_retriever = new Class_Tag_Retriever();
    }
    public function get_tag_list(\ReflectionClass $reflection_class): array
    {
        return array_merge($this->class_retriever->get_tag_list($reflection_class), $this->get_interfaces_tag_list($reflection_class));
    }
    /**
     * @param \ReflectionClass<object> $reflectionClass
     *
     * @return list<Method>
     */
    private function get_interfaces_tag_list(\ReflectionClass $reflection_class): array
    {
        $interfaces = $reflection_class->get_interfaces();
        $tag_list = [];
        foreach ($interfaces as $interface) {
            $tag_list = array_merge($tag_list, $this->class_retriever->get_tag_list($interface));
        }
        return $tag_list;
    }
}