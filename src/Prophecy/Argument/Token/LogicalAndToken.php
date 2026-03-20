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
 * Logical AND token.
 *
 * @author Boris Mikhaylov <kaguxmail@gmail.com>
 */
class Logical_And_Token implements Token_Interface
{
    /**
     * @var list<TokenInterface>
     */
    private array $tokens = [];
    /**
     * @param array<mixed> $arguments exact values or tokens
     */
    public function __construct(array $arguments)
    {
        foreach ($arguments as $argument) {
            if (!$argument instanceof Token_Interface) {
                $argument = new Exact_Value_Token($argument);
            }
            $this->tokens[] = $argument;
        }
    }
    /**
     * Scores maximum score from scores returned by tokens for this argument if all of them score.
     *
     * @param mixed $argument
     *
     * @return false|int
     */
    public function score_argument($argument)
    {
        if (0 === count($this->tokens)) {
            return false;
        }
        $max_score = 0;
        foreach ($this->tokens as $token) {
            $score = $token->score_argument($argument);
            if (false === $score) {
                return false;
            }
            $max_score = max($score, $max_score);
        }
        return $max_score;
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
        return sprintf('bool(%s)', implode(' AND ', $this->tokens));
    }
}