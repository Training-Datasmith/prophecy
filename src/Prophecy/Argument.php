<?php

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
    public static function exact($value): \Prophecy\Argument\Token\ExactValueToken
    {
        return new Token\ExactValueToken($value);
    }

    /**
     * Checks that argument is of specific type or instance of specific class.
     *
     * @param string $type Type name (`integer`, `string`) or full class name
     */
    public static function type($type): \Prophecy\Argument\Token\TypeToken
    {
        return new Token\TypeToken($type);
    }

    /**
     * Checks that argument object has specific state.
     *
     * @param string $methodName
     * @param mixed  $value
     */
    public static function which($methodName, $value): \Prophecy\Argument\Token\ObjectStateToken
    {
        return new Token\ObjectStateToken($methodName, $value);
    }

    /**
     * Checks that argument matches provided callback.
     *
     * @param callable $callback
     * @param string|null $customStringRepresentation Customize the __toString() representation of this token
     */
    public static function that($callback, ?string $customStringRepresentation = null): \Prophecy\Argument\Token\CallbackToken
    {
        return new Token\CallbackToken($callback, $customStringRepresentation);
    }

    /**
     * Matches any single value.
     */
    public static function any(): \Prophecy\Argument\Token\AnyValueToken
    {
        return new Token\AnyValueToken();
    }

    /**
     * Matches all values to the rest of the signature.
     */
    public static function cetera(): \Prophecy\Argument\Token\AnyValuesToken
    {
        return new Token\AnyValuesToken();
    }

    /**
     * Checks that argument matches all tokens
     *
     * @param mixed ...$tokens a list of tokens
     */
    public static function allOf(...$tokens): \Prophecy\Argument\Token\LogicalAndToken
    {
        return new Token\LogicalAndToken($tokens);
    }

    /**
     * Checks that argument array or countable object has exact number of elements.
     *
     * @param integer $value array elements count
     */
    public static function size($value): \Prophecy\Argument\Token\ArrayCountToken
    {
        return new Token\ArrayCountToken($value);
    }

    /**
     * Checks that argument array contains (key, value) pair
     *
     * @param mixed $key   exact value or token
     * @param mixed $value exact value or token
     */
    public static function withEntry($key, $value): \Prophecy\Argument\Token\ArrayEntryToken
    {
        return new Token\ArrayEntryToken($key, $value);
    }

    /**
     * Checks that arguments array entries all match value
     *
     * @param mixed $value
     */
    public static function withEveryEntry($value): \Prophecy\Argument\Token\ArrayEveryEntryToken
    {
        return new Token\ArrayEveryEntryToken($value);
    }

    /**
     * Checks that argument array contains value
     *
     * @param mixed $value
     */
    public static function containing($value): \Prophecy\Argument\Token\ArrayEntryToken
    {
        return new Token\ArrayEntryToken(self::any(), $value);
    }

    /**
     * Checks that argument array has key
     *
     * @param mixed $key exact value or token
     */
    public static function withKey($key): \Prophecy\Argument\Token\ArrayEntryToken
    {
        return new Token\ArrayEntryToken($key, self::any());
    }

    /**
     * Checks that argument does not match the value|token.
     *
     * @param mixed $value either exact value or argument token
     */
    public static function not($value): \Prophecy\Argument\Token\LogicalNotToken
    {
        return new Token\LogicalNotToken($value);
    }

    /**
     * @param string $value
     */
    public static function containingString($value): \Prophecy\Argument\Token\StringContainsToken
    {
        return new Token\StringContainsToken($value);
    }

    /**
     * Checks that argument is identical value.
     *
     * @param mixed $value
     */
    public static function is($value): \Prophecy\Argument\Token\IdenticalValueToken
    {
        return new Token\IdenticalValueToken($value);
    }

    /**
     * Check that argument is same value when rounding to the
     * given precision.
     *
     * @param float $value
     * @param int $precision
     */
    public static function approximate($value, $precision = 0): \Prophecy\Argument\Token\ApproximateValueToken
    {
        return new Token\ApproximateValueToken($value, $precision);
    }

    /**
     * Checks that argument is in array.
     *
     * @param array<mixed> $value
     */
    public static function in($value): \Prophecy\Argument\Token\InArrayToken
    {
        return new Token\InArrayToken($value);
    }

    /**
     * Checks that argument is not in array.
     *
     * @param array<mixed> $value
     */
    public static function notIn($value): \Prophecy\Argument\Token\NotInArrayToken
    {
        return new Token\NotInArrayToken($value);
    }

}
