<?php

namespace Prophecy\Doubler\Generator;

/**
 * Tells whether a keyword refers to a class or to a built-in type for the
 * current version of php
 *
 * @deprecated in favour of Node\TypeNodeAbstract
 */
final class TypeHintReference
{
    /**
     * @param string $type
     *
     * @return bool
     */
    public function isBuiltInParamTypeHint($type)
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
    public function isBuiltInReturnTypeHint($type)
    {
        if ($type === 'void') {
            return true;
        }

        return $this->isBuiltInParamTypeHint($type);
    }
}
