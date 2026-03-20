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
namespace Prophecy\Doubler\Generator;

use Prophecy\Doubler\Generator\Node\Type_Node_Abstract;
/**
 * Class code creator.
 * Generates PHP code for specific class node tree.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Class_Code_Generator
{
    /**
     * Generates PHP code for class node.
     *
     * @param string         $classname
     *
     */
    public function generate($classname, Node\Class_Node $class): string
    {
        $parts = explode('\\', $classname);
        $classname = array_pop($parts);
        $namespace = implode('\\', $parts);
        $code = sprintf("%sclass %s extends \\%s implements %s {\n", $class->is_read_only() ? 'readonly ' : '', $classname, $class->get_parent_class(), implode(', ', array_map(fn(string $interface) => '\\' . $interface, $class->get_interfaces())));
        foreach ($class->get_properties() as $name => $visibility) {
            $code .= sprintf("%s \$%s;\n", $visibility, $name);
        }
        $code .= "\n";
        foreach ($class->get_methods() as $method) {
            $code .= $this->generate_method($method) . "\n";
        }
        $code .= "\n}";
        return sprintf("namespace %s {\n%s\n}", $namespace, $code);
    }
    private function generate_method(Node\Method_Node $method): string
    {
        $php = sprintf("%s %s function %s%s(%s)%s {\n", $method->get_visibility(), $method->is_static() ? 'static' : '', $method->returns_reference() ? '&' : '', $method->get_name(), implode(', ', $this->generate_arguments($method->get_arguments())), ($ret = $this->generate_types($method->get_return_type_node())) ? ': ' . $ret : '');
        $php .= $method->get_code() . "\n";
        return $php . '}';
    }
    private function generate_types(Type_Node_Abstract $type_node): string
    {
        if ($type_node->get_type() === null) {
            return '';
        }
        return (string) $type_node->get_type();
    }
    /**
     * @param list<Node\ArgumentNode> $arguments
     *
     * @return list<string>
     */
    private function generate_arguments(array $arguments): array
    {
        return array_map(function (Node\Argument_Node $argument): string {
            $php = $this->generate_types($argument->get_type_node());
            $php .= ' ' . ($argument->is_passed_by_reference() ? '&' : '');
            $php .= $argument->is_variadic() ? '...' : '';
            $php .= '$' . $argument->get_name();
            if ($argument->is_optional() && !$argument->is_variadic()) {
                $default = var_export($argument->get_default(), true);
                // This is necessary for PHP 8.1, as enum cases are exported without a leading slash in this version
                if ($argument->get_default() instanceof \Unit_Enum && !str_starts_with($default, '\\')) {
                    $default = '\\' . $default;
                }
                $php .= ' = ' . $default;
            }
            return $php;
        }, $arguments);
    }
}