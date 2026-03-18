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
 * Approximate value token
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class ApproximateValueToken implements TokenInterface
{
    /**
     * @param float $value
     * @param int $precision
     */
    public function __construct(private $value, private $precision = 0)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function scoreArgument($argument): false|int
    {
        if (!\is_float($argument) && !\is_int($argument) && !\is_numeric($argument)) {
            return false;
        }

        return round((float) $argument, $this->precision) === round($this->value, $this->precision) ? 10 : false;
    }

    /**
     * {@inheritdoc}
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
        return sprintf('≅%s', round($this->value, $this->precision));
    }
}
