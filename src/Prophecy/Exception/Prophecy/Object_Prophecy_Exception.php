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

use Prophecy\Prophecy\Object_Prophecy;
class Object_Prophecy_Exception extends \RuntimeException implements Prophecy_Exception
{
    /**
     * @param string                 $message
     * @param ObjectProphecy<object> $objectProphecy
     */
    public function __construct($message, private readonly Object_Prophecy $object_prophecy, ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
    /**
     * @return ObjectProphecy<object>
     */
    public function get_object_prophecy()
    {
        return $this->object_prophecy;
    }
}