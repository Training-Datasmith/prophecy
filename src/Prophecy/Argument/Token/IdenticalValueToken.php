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

use Prophecy\Util\StringUtil;

/**
 * Identical value token.
 *
 * @author Florian Voutzinos <florian@voutzinos.com>
 */
class IdenticalValueToken implements TokenInterface
{
    private ?string $string = null;
    private readonly \Prophecy\Util\StringUtil $util;

    /**
     * Initializes token.
     *
     * @param mixed $value
     */
    public function __construct(private $value, ?StringUtil $util = null)
    {
        $this->util  = $util ?: new StringUtil();
    }

    /**
     * Scores 11 if argument matches preset value.
     *
     * @param mixed $argument
     *
     * @return false|int
     */
    public function scoreArgument($argument): int|false
    {
        return $argument === $this->value ? 11 : false;
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
        if (null === $this->string) {
            $this->string = sprintf('identical(%s)', $this->util->stringify($this->value));
        }

        return $this->string;
    }
}
