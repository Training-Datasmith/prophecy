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
namespace Prophecy\Prediction;

use Prophecy\Exception\Prediction\Unexpected_Calls_Exception;
use Prophecy\Prophecy\Method_Prophecy;
use Prophecy\Prophecy\Object_Prophecy;
use Prophecy\Util\String_Util;
/**
 * Tests that there were no calls made.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class No_Calls_Prediction implements Prediction_Interface
{
    private readonly \Prophecy\Util\String_Util $util;
    public function __construct(?String_Util $util = null)
    {
        $this->util = $util ?: new String_Util();
    }
    public function check(array $calls, Object_Prophecy $object, Method_Prophecy $method): void
    {
        if (!count($calls)) {
            return;
        }
        $verb = count($calls) === 1 ? 'was' : 'were';
        throw new Unexpected_Calls_Exception(sprintf("No calls expected that match:\n" . "  %s->%s(%s)\n" . "but %d %s made:\n%s", $object->reveal()::class, $method->get_method_name(), $method->get_arguments_wildcard(), count($calls), $verb, $this->util->stringify_calls($calls)), $method, $calls);
    }
}