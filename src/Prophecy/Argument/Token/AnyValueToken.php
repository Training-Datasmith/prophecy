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
 * Any single value token.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class AnyValueToken implements TokenInterface
{
    /**
     * Always scores 3 for any argument.
     *
     * @param mixed $argument
     */
    public function scoreArgument($argument): int
    {
        return 3;
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
        return '*';
    }
}
