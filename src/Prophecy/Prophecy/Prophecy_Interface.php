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
 * Core Prophecy interface.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * @template-covariant T of object
 */
interface Prophecy_Interface
{
    /**
     * Reveals prophecy object (double) .
     *
     * @return object
     *
     * @phpstan-return T
     */
    public function reveal();
}