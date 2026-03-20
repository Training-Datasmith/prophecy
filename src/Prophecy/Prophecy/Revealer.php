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
namespace Prophecy\Prophecy;

/**
 * Basic prophecies revealer.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Revealer implements Revealer_Interface
{
    /**
     * Unwraps value(s).
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function reveal($value)
    {
        if (is_array($value)) {
            return array_map([$this, __FUNCTION__], $value);
        }
        if (!is_object($value)) {
            return $value;
        }
        if ($value instanceof Prophecy_Interface) {
            return $value->reveal();
        }
        return $value;
    }
}