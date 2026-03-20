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
namespace Prophecy\Exception\Prophecy;

use Prophecy\Prophecy\Method_Prophecy;
class Method_Prophecy_Exception extends Object_Prophecy_Exception
{
    private readonly \Prophecy\Prophecy\Method_Prophecy $method_prophecy;
    /**
     * @param string $message
     */
    public function __construct($message, Method_Prophecy $method_prophecy, ?\Throwable $previous = null)
    {
        parent::__construct($message, $method_prophecy->get_object_prophecy(), $previous);
        $this->method_prophecy = $method_prophecy;
    }
    /**
     * @return MethodProphecy
     */
    public function get_method_prophecy()
    {
        return $this->method_prophecy;
    }
}