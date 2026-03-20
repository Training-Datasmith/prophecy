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

use Prophecy\Argument\Arguments_Wildcard;
use Prophecy\Argument\Token\Any_Values_Token;
use Prophecy\Exception\Prediction\Unexpected_Calls_Count_Exception;
use Prophecy\Prophecy\Method_Prophecy;
use Prophecy\Prophecy\Object_Prophecy;
use Prophecy\Util\String_Util;
/**
 * Tests that there was exact amount of calls made.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Call_Times_Prediction implements Prediction_Interface
{
    private readonly int $times;
    private readonly \Prophecy\Util\String_Util $util;
    /**
     * @param int        $times
     */
    public function __construct($times, ?String_Util $util = null)
    {
        $this->times = intval($times);
        $this->util = $util ?: new String_Util();
    }
    public function check(array $calls, Object_Prophecy $object, Method_Prophecy $method): void
    {
        if ($this->times == count($calls)) {
            return;
        }
        $method_calls = $object->find_prophecy_method_calls($method->get_method_name(), new Arguments_Wildcard([new Any_Values_Token()]));
        if (count($calls)) {
            $message = sprintf("Expected exactly %d calls that match:\n" . "  %s->%s(%s)\n" . "but %d were made:\n%s", $this->times, $object->reveal()::class, $method->get_method_name(), $method->get_arguments_wildcard(), count($calls), $this->util->stringify_calls($calls));
        } elseif (count($method_calls)) {
            $message = sprintf("Expected exactly %d calls that match:\n" . "  %s->%s(%s)\n" . "but none were made.\n" . "Recorded `%s(...)` calls:\n%s", $this->times, $object->reveal()::class, $method->get_method_name(), $method->get_arguments_wildcard(), $method->get_method_name(), $this->util->stringify_calls($method_calls));
        } else {
            $message = sprintf("Expected exactly %d calls that match:\n" . "  %s->%s(%s)\n" . 'but none were made.', $this->times, $object->reveal()::class, $method->get_method_name(), $method->get_arguments_wildcard());
        }
        throw new Unexpected_Calls_Count_Exception($message, $method, $this->times, $calls);
    }
}