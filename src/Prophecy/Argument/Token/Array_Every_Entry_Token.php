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
 * Array every entry token.
 *
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class Array_Every_Entry_Token implements Token_Interface
{
    private readonly \Prophecy\Argument\Token\Token_Interface $value;
    /**
     * @param mixed $value exact value or token
     */
    public function __construct($value)
    {
        if (!$value instanceof Token_Interface) {
            $value = new Exact_Value_Token($value);
        }
        $this->value = $value;
    }
    /**
     * {@inheritdoc}
     */
    public function score_argument($argument): false|int|float
    {
        if (!$argument instanceof \Traversable && !is_array($argument)) {
            return false;
        }
        $scores = [];
        foreach ($argument as $argument_entry) {
            $scores[] = $this->value->score_argument($argument_entry);
        }
        if (empty($scores) || in_array(false, $scores, true)) {
            return false;
        }
        return array_sum($scores) / count($scores);
    }
    /**
     * {@inheritdoc}
     */
    public function is_last(): bool
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
    public function get_value()
    {
        return $this->value;
    }
}