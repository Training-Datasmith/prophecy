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
namespace Prophecy\Call;

use Prophecy\Argument\Arguments_Wildcard;
use Prophecy\Exception\Call\Unexpected_Call_Exception;
use Prophecy\Exception\Prophecy\Method_Prophecy_Exception;
use Prophecy\Prophecy\Method_Prophecy;
use Prophecy\Prophecy\Object_Prophecy;
use Prophecy\Util\String_Util;
use Spl_Object_Storage;
/**
 * Calls receiver & manager.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Call_Center
{
    private readonly \Prophecy\Util\String_Util $util;
    /**
     * @var Call[]
     */
    private array $recorded_calls = [];
    /**
     * @var SplObjectStorage<Call, ObjectProphecy<object>>
     */
    private \Spl_Object_Storage $unexpected_calls;
    /**
     * Initializes call center.
     *
     * @param StringUtil $util
     */
    public function __construct(?String_Util $util = null)
    {
        $this->util = $util ?: new String_Util();
        $this->unexpected_calls = new Spl_Object_Storage();
    }
    /**
     * Makes and records specific method call for object prophecy.
     *
     * @param ObjectProphecy<object> $prophecy
     * @param string         $methodName
     * @param array<mixed>          $arguments
     *
     * @return mixed Returns null if no promise for prophecy found or promise return value.
     *
     * @throws \Prophecy\Exception\Call\UnexpectedCallException If no appropriate method prophecy found
     */
    public function make_call(Object_Prophecy $prophecy, $method_name, array $arguments)
    {
        // For efficiency exclude 'args' from the generated backtrace
        // Limit backtrace to last 3 calls as we don't use the rest
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        $file = $line = null;
        if (isset($backtrace[2]) && isset($backtrace[2]['file']) && isset($backtrace[2]['line'])) {
            $file = $backtrace[2]['file'];
            $line = $backtrace[2]['line'];
        }
        // If no method prophecies defined, then it's a dummy, so we'll just return null
        if ('__destruct' === strtolower($method_name) || 0 == count($prophecy->get_method_prophecies())) {
            $this->recorded_calls[] = new Call($method_name, $arguments, null, null, $file, $line);
            return null;
        }
        // There are method prophecies, so it's a fake/stub. Searching prophecy for this call
        $matches = $this->find_method_prophecies($prophecy, $method_name, $arguments);
        // If fake/stub doesn't have method prophecy for this call - throw exception
        if (!count($matches)) {
            $this->unexpected_calls->offsetSet(new Call($method_name, $arguments, null, null, $file, $line), $prophecy);
            $this->recorded_calls[] = new Call($method_name, $arguments, null, null, $file, $line);
            return null;
        }
        // Sort matches by their score value
        @usort($matches, fn(array $match1, array $match2) => $match2[0] - $match1[0]);
        $score = $matches[0][0];
        // If Highest rated method prophecy has a promise - execute it or return null instead
        $method_prophecy = $matches[0][1];
        $return_value = null;
        $exception = null;
        if ($promise = $method_prophecy->get_promise()) {
            try {
                $return_value = $promise->execute($arguments, $prophecy, $method_prophecy);
            } catch (\Exception $e) {
                $exception = $e;
            }
        }
        if ($method_prophecy->has_return_void() && $return_value !== null) {
            throw new Method_Prophecy_Exception("The method \"{$method_name}\" has a void return type, but the promise returned a value", $method_prophecy);
        }
        $this->recorded_calls[] = $call = new Call($method_name, $arguments, $return_value, $exception, $file, $line);
        $call->add_score($method_prophecy->get_arguments_wildcard(), $score);
        if (null !== $exception) {
            throw $exception;
        }
        return $return_value;
    }
    /**
     * Searches for calls by method name & arguments wildcard.
     *
     * @param string            $methodName
     *
     * @return list<Call>
     */
    public function find_calls($method_name, Arguments_Wildcard $wildcard): array
    {
        $method_name = strtolower($method_name);
        return array_values(array_filter($this->recorded_calls, fn(Call $call) => $method_name === strtolower($call->get_method_name()) && 0 < $call->get_score($wildcard)));
    }
    /**
     * @throws UnexpectedCallException
     */
    public function check_unexpected_calls(): void
    {
        foreach ($this->unexpected_calls as $call) {
            $prophecy = $this->unexpected_calls[$call];
            // If fake/stub doesn't have method prophecy for this call - throw exception
            if (!count($this->find_method_prophecies($prophecy, $call->get_method_name(), $call->get_arguments()))) {
                throw $this->create_unexpected_call_exception($prophecy, $call->get_method_name(), $call->get_arguments());
            }
        }
    }
    /**
     * @param ObjectProphecy<object> $prophecy
     * @param string                 $methodName
     * @param array<mixed>           $arguments
     */
    private function create_unexpected_call_exception(Object_Prophecy $prophecy, $method_name, array $arguments): \Prophecy\Exception\Call\Unexpected_Call_Exception
    {
        $classname = $prophecy->reveal()::class;
        $indentation_length = 8;
        // looks good
        $argstring = implode(",\n", $this->indent_arguments(array_map($this->util->stringify(...), $arguments), $indentation_length));
        $expected = [];
        foreach (array_merge(...array_values($prophecy->get_method_prophecies())) as $method_prophecy) {
            $expected[] = sprintf("  - %s(\n" . "%s\n" . '    )', $method_prophecy->get_method_name(), implode(",\n", $this->indent_arguments(array_map(strval(...), $method_prophecy->get_arguments_wildcard()->get_tokens()), $indentation_length)));
        }
        return new Unexpected_Call_Exception(sprintf("Unexpected method call on %s:\n" . "  - %s(\n" . "%s\n" . "    )\n" . "expected calls were:\n" . '%s', $classname, $method_name, $argstring, implode("\n", $expected)), $prophecy, $method_name, $arguments);
    }
    /**
     * @param string[] $arguments
     *
     * @return string[]
     */
    private function indent_arguments(array $arguments, int $indentation_length): array
    {
        return preg_replace_callback('/^/m', fn() => str_repeat(' ', $indentation_length), $arguments);
    }
    /**
     * @param ObjectProphecy<object> $prophecy
     * @param string $methodName
     * @param array<mixed> $arguments
     *
     *
     * @phpstan-return list<array{int, MethodProphecy}>
     */
    private function find_method_prophecies(Object_Prophecy $prophecy, $method_name, array $arguments): array
    {
        $matches = [];
        foreach ($prophecy->get_method_prophecies($method_name) as $method_prophecy) {
            if (0 < $score = $method_prophecy->get_arguments_wildcard()->score_arguments($arguments)) {
                $matches[] = [$score, $method_prophecy];
            }
        }
        return $matches;
    }
}