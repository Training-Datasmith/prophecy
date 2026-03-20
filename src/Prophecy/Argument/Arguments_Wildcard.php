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
namespace Prophecy\Argument;

/**
 * Arguments wildcarding.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Arguments_Wildcard implements \Stringable
{
    /**
     * @var list<Token\TokenInterface>
     */
    private array $tokens = [];
    private ?string $string = null;
    /**
     * Initializes wildcard.
     *
     * @param array<mixed> $arguments Array of argument tokens or values
     */
    public function __construct(array $arguments)
    {
        foreach ($arguments as $argument) {
            if (!$argument instanceof Token\Token_Interface) {
                $argument = new Token\Exact_Value_Token($argument);
            }
            $this->tokens[] = $argument;
        }
    }
    /**
     * Calculates wildcard match score for provided arguments.
     *
     * @param array<mixed> $arguments
     *
     * @return false|int False OR integer score (higher - better)
     */
    public function score_arguments(array $arguments): false|int|float
    {
        if (0 == count($arguments) && 0 == count($this->tokens)) {
            return 1;
        }
        $arguments = array_values($arguments);
        $total_score = 0;
        foreach ($this->tokens as $i => $token) {
            $argument = $arguments[$i] ?? null;
            if (1 >= $score = $token->score_argument($argument)) {
                return false;
            }
            $total_score += $score;
            if (true === $token->is_last()) {
                return $total_score;
            }
        }
        if (count($arguments) > count($this->tokens)) {
            return false;
        }
        return $total_score;
    }
    /**
     * Returns string representation for wildcard.
     */
    public function __toString(): string
    {
        if (null === $this->string) {
            $this->string = implode(', ', array_map(fn(\Prophecy\Argument\Token\Token_Interface $token) => (string) $token, $this->tokens));
        }
        return $this->string;
    }
    /**
     * @return list<Token\TokenInterface>
     */
    public function get_tokens()
    {
        return $this->tokens;
    }
}