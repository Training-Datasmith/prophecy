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
namespace Prophecy\Exception\Call;

use Prophecy\Exception\Prophecy\Object_Prophecy_Exception;
use Prophecy\Prophecy\Object_Prophecy;
class Unexpected_Call_Exception extends Object_Prophecy_Exception
{
    /**
     * @param string                 $message
     * @param ObjectProphecy<object> $objectProphecy
     * @param string                 $methodName
     * @param array<mixed>           $arguments
     */
    public function __construct($message, Object_Prophecy $object_prophecy, private $method_name, private readonly array $arguments)
    {
        parent::__construct($message, $object_prophecy);
    }
    /**
     * @return string
     */
    public function get_method_name()
    {
        return $this->method_name;
    }
    /**
     * @return array<mixed>
     */
    public function get_arguments()
    {
        return $this->arguments;
    }
}