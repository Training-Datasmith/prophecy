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
 * Logical NOT token.
 *
 * @author Boris Mikhaylov <kaguxmail@gmail.com>
 */
class Logical_Not_Token implements Token_Interface
{
    private readonly \Prophecy\Argument\Token\Token_Interface $token;
    /**
     * @param mixed $value exact value or token
     */
    public function __construct($value)
    {
        $this->token = $value instanceof Token_Interface ? $value : new Exact_Value_Token($value);
    }
    /**
     * Scores 4 when preset token does not match the argument.
     *
     * @param mixed $argument
     *
     * @return false|int
     */
    public function score_argument($argument): int|false
    {
        return false === $this->token->score_argument($argument) ? 4 : false;
    }
    /**
     * Returns true if preset token is last.
     *
     * @return bool
     */
    public function is_last()
    {
        return $this->token->is_last();
    }
    /**
     * Returns originating token.
     *
     * @return TokenInterface
     */
    public function get_originating_token()
    {
        return $this->token;
    }
    /**
     * Returns string representation for token.
     */
    public function __toString(): string
    {
        return sprintf('not(%s)', $this->token);
    }
}