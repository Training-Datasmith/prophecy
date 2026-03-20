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
namespace Prophecy\Argument\Token;

/**
 * Check if values is not in array
 *
 * @author Vinícius Alonso <vba321@hotmail.com>
 */
class Not_In_Array_Token implements Token_Interface
{
    /**
     * @param array<mixed> $token tokens
     * @param bool $strict
     */
    public function __construct(private readonly array $token, private $strict = true)
    {
    }
    /**
     * Return scores 8 score if argument is in array.
     *
     * @param $argument
     */
    public function score_argument($argument): false|int
    {
        if (count($this->token) === 0) {
            return false;
        }
        if (!\in_array($argument, $this->token, $this->strict)) {
            return 8;
        }
        return false;
    }
    /**
     * Returns false.
     */
    public function is_last(): bool
    {
        return false;
    }
    /**
     * Returns string representation for token.
     */
    public function __toString(): string
    {
        $array_as_string = implode(', ', $this->token);
        return "[{$array_as_string}]";
    }
}