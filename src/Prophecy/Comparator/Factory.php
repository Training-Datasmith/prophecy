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

use Sebastian_Bergmann\Comparator\Factory as BaseFactory;
/**
 * Prophecy comparator factory.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * @deprecated Use "Prophecy\Comparator\FactoryProvider" instead to get a "SebastianBergmann\Comparator\Factory" instance.
 */
final class Factory extends Base_Factory
{
    private static ?\Prophecy\Comparator\Factory $instance = null;
    public function __construct()
    {
        parent::__construct();
        $this->register(new Closure_Comparator());
        $this->register(new Prophecy_Comparator());
    }
    /**
     * @return Factory
     */
    public static function get_instance()
    {
        if (self::$instance === null) {
            self::$instance = new Factory();
        }
        return self::$instance;
    }
}