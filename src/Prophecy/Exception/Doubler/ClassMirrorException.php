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
namespace Prophecy\Exception\Doubler;

use ReflectionClass;
class Class_Mirror_Exception extends \RuntimeException implements Doubler_Exception
{
    /**
     * @param string                  $message
     * @param ReflectionClass<object> $class
     */
    public function __construct($message, private readonly ReflectionClass $class)
    {
        parent::__construct($message);
    }
    /**
     * @return ReflectionClass<object>
     */
    public function get_reflected_class()
    {
        return $this->class;
    }
}