<?php

declare(strict_types=1);

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
 * Check if values is in array
 *
 * @author Vinícius Alonso <vba321@hotmail.com>
 */
class InArrayToken implements TokenInterface
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
    public function scoreArgument($argument): false|int
    {
        if (count($this->token) === 0) {
            return false;
        }

        if (\in_array($argument, $this->token, $this->strict)) {
            return 8;
        }

        return false;
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
        $arrayAsString = implode(', ', $this->token);
        return "[{$arrayAsString}]";
    }
}
