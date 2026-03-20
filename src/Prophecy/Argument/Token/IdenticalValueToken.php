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

use Prophecy\Util\String_Util;
/**
 * Identical value token.
 *
 * @author Florian Voutzinos <florian@voutzinos.com>
 */
class Identical_Value_Token implements Token_Interface
{
    private ?string $string = null;
    private readonly \Prophecy\Util\String_Util $util;
    /**
     * Initializes token.
     *
     * @param mixed $value
     */
    public function __construct(private $value, ?String_Util $util = null)
    {
        $this->util = $util ?: new String_Util();
    }
    /**
     * Scores 11 if argument matches preset value.
     *
     * @param mixed $argument
     *
     * @return false|int
     */
    public function score_argument($argument): int|false
    {
        return $argument === $this->value ? 11 : false;
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
        if (null === $this->string) {
            $this->string = sprintf('identical(%s)', $this->util->stringify($this->value));
        }
        return $this->string;
    }
}