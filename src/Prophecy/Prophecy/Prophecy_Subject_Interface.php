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
 * Controllable doubles interface.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
interface Prophecy_Subject_Interface
{
    /**
     * Sets subject prophecy.
     *
     * @param ProphecyInterface<object> $prophecy
     *
     * @return void
     */
    public function set_prophecy(Prophecy_Interface $prophecy);
    /**
     * Returns subject prophecy.
     *
     * @return ProphecyInterface<object>
     */
    public function get_prophecy();
}