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
namespace Prophecy\Promise;

use Prophecy\Prophecy\Method_Prophecy;
use Prophecy\Prophecy\Object_Prophecy;
/**
 * Promise interface.
 * Promises are logical blocks, tied to `will...` keyword.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
interface Promise_Interface
{
    /**
     * Evaluates promise.
     *
     * @param array<mixed>           $args
     * @param ObjectProphecy<object> $object
     *
     * @return mixed
     */
    public function execute(array $args, Object_Prophecy $object, Method_Prophecy $method);
}