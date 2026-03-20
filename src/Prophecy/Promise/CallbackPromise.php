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

use Closure;
use Prophecy\Exception\InvalidArgumentException;
use Prophecy\Prophecy\Method_Prophecy;
use Prophecy\Prophecy\Object_Prophecy;
use ReflectionFunction;
/**
 * Evaluates promise callback.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Callback_Promise implements Promise_Interface
{
    private $callback;
    /**
     * Initializes callback promise.
     *
     * @param callable $callback Custom callback
     *
     * @throws \Prophecy\Exception\InvalidArgumentException
     */
    public function __construct($callback)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException(sprintf('Callable expected as an argument to CallbackPromise, but got %s.', gettype($callback)));
        }
        $this->callback = $callback;
    }
    public function execute(array $args, Object_Prophecy $object, Method_Prophecy $method): mixed
    {
        $callback = $this->callback;
        if ($callback instanceof Closure && (new ReflectionFunction($callback))->get_closure_this() !== null) {
            $callback = Closure::bind($callback, $object) ?? $this->callback;
        }
        return call_user_func($callback, $args, $object, $method);
    }
}