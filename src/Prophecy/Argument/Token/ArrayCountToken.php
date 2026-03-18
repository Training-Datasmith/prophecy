<?php

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
 * Array elements count token.
 *
 * @author Boris Mikhaylov <kaguxmail@gmail.com>
 */

class ArrayCountToken implements TokenInterface
{
    /**
     * @param integer $count
     */
    public function __construct(private $count)
    {
    }

    /**
     * Scores 6 when argument has preset number of elements.
     *
     * @param mixed $argument
     *
     * @return false|int
     */
    public function scoreArgument($argument): int|false
    {
        return $this->isCountable($argument) && $this->hasProperCount($argument) ? 6 : false;
    }

    /**
     * Returns false.
     */
    public function isLast(): bool
    {
        return false;
    }

    /**
     * Returns string representation for token.
     */
    public function __toString(): string
    {
        return sprintf('count(%s)', $this->count);
    }

    /**
     * Returns true if object is either array or instance of \Countable
     *
     * @param mixed $argument
     *
     * @phpstan-assert-if-true array<mixed>|\Countable $argument
     */
    private function isCountable($argument): bool
    {
        return (is_countable($argument));
    }

    /**
     * Returns true if $argument has expected number of elements
     *
     * @param array<mixed>|\Countable $argument
     */
    private function hasProperCount(\Countable|array $argument): bool
    {
        return $this->count === count($argument);
    }
}
