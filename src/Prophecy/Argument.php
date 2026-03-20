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
namespace Prophecy;

use Prophecy\Argument\Token;
/**
 * Argument tokens shortcuts.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Argument
{
    /**
     * Checks that argument is exact value or object.
     *
     * @param mixed $value
     */
    public static function exact($value): \Prophecy\Argument\Token\Exact_Value_Token
    {
        return new Token\Exact_Value_Token($value);
    }
    /**
     * Checks that argument is of specific type or instance of specific class.
     *
     * @param string $type Type name (`integer`, `string`) or full class name
     */
    public static function type($type): \Prophecy\Argument\Token\Type_Token
    {
        return new Token\Type_Token($type);
    }
    /**
     * Checks that argument object has specific state.
     *
     * @param string $methodName
     * @param mixed  $value
     */
    public static function which($method_name, $value): \Prophecy\Argument\Token\Object_State_Token
    {
        return new Token\Object_State_Token($method_name, $value);
    }
    /**
     * Checks that argument matches provided callback.
     *
     * @param callable $callback
     * @param string|null $customStringRepresentation Customize the __toString() representation of this token
     */
    public static function that($callback, ?string $custom_string_representation = null): \Prophecy\Argument\Token\Callback_Token
    {
        return new Token\Callback_Token($callback, $custom_string_representation);
    }
    /**
     * Matches any single value.
     */
    public static function any(): \Prophecy\Argument\Token\Any_Value_Token
    {
        return new Token\Any_Value_Token();
    }
    /**
     * Matches all values to the rest of the signature.
     */
    public static function cetera(): \Prophecy\Argument\Token\Any_Values_Token
    {
        return new Token\Any_Values_Token();
    }
    /**
     * Checks that argument matches all tokens
     *
     * @param mixed ...$tokens a list of tokens
     */
    public static function all_of(...$tokens): \Prophecy\Argument\Token\Logical_And_Token
    {
        return new Token\Logical_And_Token($tokens);
    }
    /**
     * Checks that argument array or countable object has exact number of elements.
     *
     * @param integer $value array elements count
     */
    public static function size($value): \Prophecy\Argument\Token\Array_Count_Token
    {
        return new Token\Array_Count_Token($value);
    }
    /**
     * Checks that argument array contains (key, value) pair
     *
     * @param mixed $key   exact value or token
     * @param mixed $value exact value or token
     */
    public static function with_entry($key, $value): \Prophecy\Argument\Token\Array_Entry_Token
    {
        return new Token\Array_Entry_Token($key, $value);
    }
    /**
     * Checks that arguments array entries all match value
     *
     * @param mixed $value
     */
    public static function with_every_entry($value): \Prophecy\Argument\Token\Array_Every_Entry_Token
    {
        return new Token\Array_Every_Entry_Token($value);
    }
    /**
     * Checks that argument array contains value
     *
     * @param mixed $value
     */
    public static function containing($value): \Prophecy\Argument\Token\Array_Entry_Token
    {
        return new Token\Array_Entry_Token(self::any(), $value);
    }
    /**
     * Checks that argument array has key
     *
     * @param mixed $key exact value or token
     */
    public static function with_key($key): \Prophecy\Argument\Token\Array_Entry_Token
    {
        return new Token\Array_Entry_Token($key, self::any());
    }
    /**
     * Checks that argument does not match the value|token.
     *
     * @param mixed $value either exact value or argument token
     */
    public static function not($value): \Prophecy\Argument\Token\Logical_Not_Token
    {
        return new Token\Logical_Not_Token($value);
    }
    /**
     * @param string $value
     */
    public static function containing_string($value): \Prophecy\Argument\Token\String_Contains_Token
    {
        return new Token\String_Contains_Token($value);
    }
    /**
     * Checks that argument is identical value.
     *
     * @param mixed $value
     */
    public static function is($value): \Prophecy\Argument\Token\Identical_Value_Token
    {
        return new Token\Identical_Value_Token($value);
    }
    /**
     * Check that argument is same value when rounding to the
     * given precision.
     *
     * @param float $value
     * @param int $precision
     */
    public static function approximate($value, $precision = 0): \Prophecy\Argument\Token\Approximate_Value_Token
    {
        return new Token\Approximate_Value_Token($value, $precision);
    }
    /**
     * Checks that argument is in array.
     *
     * @param array<mixed> $value
     */
    public static function in($value): \Prophecy\Argument\Token\In_Array_Token
    {
        return new Token\In_Array_Token($value);
    }
    /**
     * Checks that argument is not in array.
     *
     * @param array<mixed> $value
     */
    public static function not_in($value): \Prophecy\Argument\Token\Not_In_Array_Token
    {
        return new Token\Not_In_Array_Token($value);
    }
}