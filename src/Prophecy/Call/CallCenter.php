<?php

declare(strict_types=1);

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prophecy\Call;

use Prophecy\Argument\ArgumentsWildcard;
use Prophecy\Exception\Call\UnexpectedCallException;
use Prophecy\Exception\Prophecy\MethodProphecyException;
use Prophecy\Prophecy\MethodProphecy;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Util\StringUtil;
use SplObjectStorage;

/**
 * Calls receiver & manager.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class CallCenter
{
    private readonly \Prophecy\Util\StringUtil $util;

    /**
     * @var Call[]
     */
    private array $recordedCalls = [];

    /**
     * @var SplObjectStorage<Call, ObjectProphecy<object>>
     */
    private \SplObjectStorage $unexpectedCalls;

    /**
     * Initializes call center.
     *
     * @param StringUtil $util
     */
    public function __construct(?StringUtil $util = null)
    {
        $this->util = $util ?: new StringUtil();
        $this->unexpectedCalls = new SplObjectStorage();
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
    public function makeCall(ObjectProphecy $prophecy, $methodName, array $arguments)
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
        if ('__destruct' === strtolower($methodName) || 0 == count($prophecy->getMethodProphecies())) {
            $this->recordedCalls[] = new Call($methodName, $arguments, null, null, $file, $line);

            return null;
        }

        // There are method prophecies, so it's a fake/stub. Searching prophecy for this call
        $matches = $this->findMethodProphecies($prophecy, $methodName, $arguments);

        // If fake/stub doesn't have method prophecy for this call - throw exception
        if (!count($matches)) {
            $this->unexpectedCalls->offsetSet(new Call($methodName, $arguments, null, null, $file, $line), $prophecy);
            $this->recordedCalls[] = new Call($methodName, $arguments, null, null, $file, $line);

            return null;
        }

        // Sort matches by their score value
        @usort($matches, fn (array $match1, array $match2) => $match2[0] - $match1[0]);

        $score = $matches[0][0];
        // If Highest rated method prophecy has a promise - execute it or return null instead
        $methodProphecy = $matches[0][1];
        $returnValue = null;
        $exception   = null;
        if ($promise = $methodProphecy->getPromise()) {
            try {
                $returnValue = $promise->execute($arguments, $prophecy, $methodProphecy);
            } catch (\Exception $e) {
                $exception = $e;
            }
        }

        if ($methodProphecy->hasReturnVoid() && $returnValue !== null) {
            throw new MethodProphecyException(
                "The method \"$methodName\" has a void return type, but the promise returned a value",
                $methodProphecy
            );
        }

        $this->recordedCalls[] = $call = new Call(
            $methodName,
            $arguments,
            $returnValue,
            $exception,
            $file,
            $line
        );
        $call->addScore($methodProphecy->getArgumentsWildcard(), $score);

        if (null !== $exception) {
            throw $exception;
        }

        return $returnValue;
    }

    /**
     * Searches for calls by method name & arguments wildcard.
     *
     * @param string            $methodName
     *
     * @return list<Call>
     */
    public function findCalls($methodName, ArgumentsWildcard $wildcard): array
    {
        $methodName = strtolower($methodName);

        return array_values(
            array_filter($this->recordedCalls, fn (Call $call) => $methodName === strtolower($call->getMethodName())
                && 0 < $call->getScore($wildcard))
        );
    }

    /**
     * @throws UnexpectedCallException
     */
    public function checkUnexpectedCalls(): void
    {
        foreach ($this->unexpectedCalls as $call) {
            $prophecy = $this->unexpectedCalls[$call];

            // If fake/stub doesn't have method prophecy for this call - throw exception
            if (!count($this->findMethodProphecies($prophecy, $call->getMethodName(), $call->getArguments()))) {
                throw $this->createUnexpectedCallException($prophecy, $call->getMethodName(), $call->getArguments());
            }
        }
    }

    /**
     * @param ObjectProphecy<object> $prophecy
     * @param string                 $methodName
     * @param array<mixed>           $arguments
     */
    private function createUnexpectedCallException(
        ObjectProphecy $prophecy,
        $methodName,
        array $arguments
    ): \Prophecy\Exception\Call\UnexpectedCallException {
        $classname = $prophecy->reveal()::class;
        $indentationLength = 8; // looks good
        $argstring = implode(
            ",\n",
            $this->indentArguments(
                array_map($this->util->stringify(...), $arguments),
                $indentationLength
            )
        );

        $expected = [];

        foreach (array_merge(...array_values($prophecy->getMethodProphecies())) as $methodProphecy) {
            $expected[] = sprintf(
                "  - %s(\n"
                ."%s\n"
                .'    )',
                $methodProphecy->getMethodName(),
                implode(
                    ",\n",
                    $this->indentArguments(
                        array_map(strval(...), $methodProphecy->getArgumentsWildcard()->getTokens()),
                        $indentationLength
                    )
                )
            );
        }

        return new UnexpectedCallException(
            sprintf(
                "Unexpected method call on %s:\n"
                ."  - %s(\n"
                ."%s\n"
                ."    )\n"
                ."expected calls were:\n"
                .'%s',
                $classname,
                $methodName,
                $argstring,
                implode("\n", $expected)
            ),
            $prophecy,
            $methodName,
            $arguments
        );
    }

    /**
     * @param string[] $arguments
     *
     * @return string[]
     */
    private function indentArguments(array $arguments, int $indentationLength): array
    {
        return preg_replace_callback(
            '/^/m',
            fn () => str_repeat(' ', $indentationLength),
            $arguments
        );
    }

    /**
     * @param ObjectProphecy<object> $prophecy
     * @param string $methodName
     * @param array<mixed> $arguments
     *
     *
     * @phpstan-return list<array{int, MethodProphecy}>
     */
    private function findMethodProphecies(ObjectProphecy $prophecy, $methodName, array $arguments): array
    {
        $matches = [];
        foreach ($prophecy->getMethodProphecies($methodName) as $methodProphecy) {
            if (0 < $score = $methodProphecy->getArgumentsWildcard()->scoreArguments($arguments)) {
                $matches[] = [$score, $methodProphecy];
            }
        }

        return $matches;
    }
}
