<?php

declare (strict_types=1);
namespace Prophecy\Doubler\Generator;

/**
 * Tells whether a keyword refers to a class or to a built-in type for the
 * current version of php
 *
 * @deprecated in favour of Node\TypeNodeAbstract
 */
final class Type_Hint_Reference
{
    /**
     * @param string $type
     *
     * @return bool
     */
    public function is_built_in_param_type_hint($type)
    {
        return match ($type) {
            'self', 'array', 'callable', 'bool', 'float', 'int', 'string', 'iterable', 'object', 'mixed' => true,
            default => false,
        };
    }
    /**
     * @param string $type
     *
     * @return bool
     */
    public function is_built_in_return_type_hint($type)
    {
        if ($type === 'void') {
            return true;
        }
        return $this->is_built_in_param_type_hint($type);
    }
}