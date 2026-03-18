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
 * Array every entry token.
 *
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class ArrayEveryEntryToken implements TokenInterface
{
    private readonly \Prophecy\Argument\Token\TokenInterface $value;

    /**
     * @param mixed $value exact value or token
     */
    public function __construct($value)
    {
        if (!$value instanceof TokenInterface) {
            $value = new ExactValueToken($value);
        }

        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function scoreArgument($argument): false|int|float
    {
        if (!$argument instanceof \Traversable && !is_array($argument)) {
            return false;
        }

        $scores = [];
        foreach ($argument as $argumentEntry) {
            $scores[] = $this->value->scoreArgument($argumentEntry);
        }

        if (empty($scores) || in_array(false, $scores, true)) {
            return false;
        }

        return array_sum($scores) / count($scores);
    }

    /**
     * {@inheritdoc}
     */
    public function isLast(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return sprintf('[%s, ..., %s]', $this->value, $this->value);
    }

    /**
     * @return TokenInterface
     */
    public function getValue()
    {
        return $this->value;
    }
}
