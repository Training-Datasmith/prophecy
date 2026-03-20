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
namespace Prophecy\Comparator;

use Sebastian_Bergmann\Comparator\Factory;
/**
 * Prophecy comparator factory.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
final class Factory_Provider
{
    private static ?\Sebastian_Bergmann\Comparator\Factory $instance = null;
    private function __construct()
    {
    }
    public static function get_instance(): Factory
    {
        if (self::$instance === null) {
            self::$instance = new Factory();
            self::$instance->register(new Closure_Comparator());
            self::$instance->register(new Prophecy_Comparator());
        }
        return self::$instance;
    }
}