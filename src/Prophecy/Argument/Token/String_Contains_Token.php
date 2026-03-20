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
 * String contains token.
 *
 * @author Peter Mitchell <pete@peterjmit.com>
 */
class String_Contains_Token implements Token_Interface
{
    /**
     * Initializes token.
     *
     * @param string $value
     */
    public function __construct(private $value)
    {
    }
    public function score_argument($argument): int|false
    {
        return is_string($argument) && str_contains($argument, $this->value) ? 6 : false;
    }
    /**
     * Returns preset value against which token checks arguments.
     *
     * @return mixed
     */
    public function get_value()
    {
        return $this->value;
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
        return sprintf('contains("%s")', $this->value);
    }
}