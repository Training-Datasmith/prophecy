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
use Prophecy\Call\Call;
use Prophecy\Exception\Prediction\No_Calls_Exception;
use Prophecy\Prophecy\Method_Prophecy;
use Prophecy\Prophecy\Object_Prophecy;
use Prophecy\Util\String_Util;
/**
 * Tests that there was at least one call.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Call_Prediction implements Prediction_Interface
{
    private readonly \Prophecy\Util\String_Util $util;
    public function __construct(?String_Util $util = null)
    {
        $this->util = $util ?: new String_Util();
    }
    public function check(array $calls, Object_Prophecy $object, Method_Prophecy $method): void
    {
        if (count($calls)) {
            return;
        }
        $method_calls = $object->find_prophecy_method_calls($method->get_method_name(), new Arguments_Wildcard([new Any_Values_Token()]));
        if (count($method_calls)) {
            throw new No_Calls_Exception(sprintf("No calls have been made that match:\n" . "  %s->%s(%s)\n" . "but expected at least one.\n" . "Recorded `%s(...)` calls:\n%s", $object->reveal()::class, $method->get_method_name(), $method->get_arguments_wildcard(), $method->get_method_name(), $this->util->stringify_calls($method_calls)), $method);
        }
        throw new No_Calls_Exception(sprintf("No calls have been made that match:\n" . "  %s->%s(%s)\n" . 'but expected at least one.', $object->reveal()::class, $method->get_method_name(), $method->get_arguments_wildcard()), $method);
    }
}