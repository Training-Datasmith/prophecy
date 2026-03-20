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
namespace Prophecy\Prophecy;

use Prophecy\Argument;
use Prophecy\Exception\Doubler\Class_Mirror_Exception;
use Prophecy\Exception\Doubler\Method_Not_Found_Exception;
use Prophecy\Exception\InvalidArgumentException;
use Prophecy\Exception\Prediction\Prediction_Exception;
use Prophecy\Exception\Prophecy\Method_Prophecy_Exception;
use Prophecy\Prediction;
use Prophecy\Promise;
use Prophecy\Prophet;
use ReflectionNamedType;
use ReflectionUnionType;
/**
 * Method prophecy.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Method_Prophecy
{
    private readonly \Prophecy\Prophecy\Object_Prophecy $object_prophecy;
    private $method_name;
    private \Prophecy\Argument\Arguments_Wildcard $arguments_wildcard;
    /**
     * @var Promise\PromiseInterface|null
     */
    private $promise;
    /**
     * @var Prediction\PredictionInterface|null
     */
    private $prediction;
    /**
     * @var list<Prediction\PredictionInterface>
     */
    private $checked_predictions = [];
    private bool $bound = false;
    private bool $void_return_type = false;
    /**
     * @param ObjectProphecy<object>                  $objectProphecy
     * @param string                                  $methodName
     * @param Argument\ArgumentsWildcard|array<mixed> $arguments
     *
     * @throws \Prophecy\Exception\Doubler\MethodNotFoundException If method not found
     *
     * @internal
     */
    public function __construct(Object_Prophecy $object_prophecy, $method_name, $arguments)
    {
        $double = $object_prophecy->reveal();
        if (!method_exists($double, $method_name)) {
            throw new Method_Not_Found_Exception(sprintf('Method `%s::%s()` is not defined.', $double::class, $method_name), $double::class, $method_name, $arguments);
        }
        $this->object_prophecy = $object_prophecy;
        $this->method_name = $method_name;
        $reflected_method = new \ReflectionMethod($double, $method_name);
        if ($reflected_method->is_final()) {
            throw new Method_Prophecy_Exception(sprintf("Can not add prophecy for a method `%s::%s()`\n" . 'as it is a final method.', $double::class, $method_name), $this);
        }
        $this->with_arguments($arguments);
        $has_tentative_return_type = method_exists($reflected_method, 'hasTentativeReturnType') && $reflected_method->has_tentative_return_type();
        if (true === $reflected_method->has_return_type() || $has_tentative_return_type) {
            if ($has_tentative_return_type) {
                $reflection_type = $reflected_method->get_tentative_return_type();
            } else {
                $reflection_type = $reflected_method->get_return_type();
            }
            if ($reflection_type instanceof ReflectionNamedType) {
                $types = [$reflection_type];
            } elseif ($reflection_type instanceof ReflectionUnionType) {
                $types = $reflection_type->get_types();
            } else {
                throw new Method_Prophecy_Exception(sprintf("Can not add prophecy for a method `%s::%s()`\nas its return type is not supported by Prophecy yet.", $double::class, $method_name), $this);
            }
            $types = array_map(fn(ReflectionNamedType $type) => $type->get_name(), $types);
            usort($types, static function (string $type1, string $type2): int {
                // null is lowest priority
                if ($type2 == 'null') {
                    return -1;
                }
                // null is lowest priority
                if ($type1 == 'null') {
                    return 1;
                }
                // objects are higher priority than scalars
                $is_object = static fn($type) => class_exists($type) || interface_exists($type);
                if ($is_object($type1) && !$is_object($type2)) {
                    return -1;
                }
                if (!$is_object($type1) && $is_object($type2)) {
                    return 1;
                }
                // don't sort both-scalars or both-objects
                return 0;
            });
            $default_type = $types[0];
            if ('void' === $default_type) {
                $this->void_return_type = true;
            }
            $this->will(function ($args, Object_Prophecy $object, Method_Prophecy $method) use ($default_type) {
                switch ($default_type) {
                    case 'void':
                        return;
                    case 'string':
                        return '';
                    case 'float':
                        return 0.0;
                    case 'int':
                        return 0;
                    case 'bool':
                    case 'false':
                        return false;
                    case 'array':
                        return [];
                    case 'true':
                        return true;
                    case 'null':
                        return null;
                    case 'callable':
                    case 'Closure':
                        return function (): void {
                        };
                    case 'Traversable':
                    case 'Generator':
                        return (function () {
                            yield;
                        })();
                    case 'object':
                        $prophet = new Prophet();
                        return $prophet->prophesize()->reveal();
                    default:
                        if (!class_exists($default_type) && !interface_exists($default_type)) {
                            throw new Method_Prophecy_Exception(sprintf('Cannot create a return value for the method as the type "%s" is not supported. Configure an explicit return value instead.', $default_type), $method);
                        }
                        $prophet = new Prophet();
                        try {
                            return $prophet->prophesize($default_type)->reveal();
                        } catch (Class_Mirror_Exception $e) {
                            throw new Method_Prophecy_Exception(\sprintf('Cannot create a return value for the method. Configure an explicit return value instead.'), $method, $e);
                        }
                }
            });
        }
    }
    /**
     * Sets argument wildcard.
     *
     * @param array<mixed>|Argument\ArgumentsWildcard $arguments
     *
     * @return $this
     *
     * @throws \Prophecy\Exception\InvalidArgumentException
     */
    public function with_arguments($arguments): static
    {
        if (is_array($arguments)) {
            $arguments = new Argument\Arguments_Wildcard($arguments);
        }
        if (!$arguments instanceof Argument\Arguments_Wildcard) {
            throw new InvalidArgumentException(sprintf("Either an array or an instance of ArgumentsWildcard expected as\n" . 'a `MethodProphecy::withArguments()` argument, but got %s.', gettype($arguments)));
        }
        $this->arguments_wildcard = $arguments;
        return $this;
    }
    /**
     * Sets custom promise to the prophecy.
     *
     * @param callable|Promise\PromiseInterface $promise
     *
     * @return $this
     *
     * @throws \Prophecy\Exception\InvalidArgumentException
     */
    public function will($promise): static
    {
        if (is_callable($promise)) {
            $promise = new Promise\Callback_Promise($promise);
        }
        if (!$promise instanceof Promise\Promise_Interface) {
            throw new InvalidArgumentException(sprintf('Expected callable or instance of PromiseInterface, but got %s.', gettype($promise)));
        }
        $this->bind_to_object_prophecy();
        $this->promise = $promise;
        return $this;
    }
    /**
     * Sets return promise to the prophecy.
     *
     * @see \Prophecy\Promise\ReturnPromise
     *
     * @param mixed ...$return a list of return values
     *
     * @return $this
     */
    public function will_return(...$return)
    {
        if ($this->void_return_type) {
            throw new Method_Prophecy_Exception("The method \"{$this->method_name}\" has a void return type, and so cannot return anything", $this);
        }
        return $this->will(new Promise\Return_Promise($return));
    }
    /**
     * @param array<mixed> $items
     * @param mixed $return
     *
     * @return $this
     *
     * @throws \Prophecy\Exception\InvalidArgumentException
     */
    public function will_yield($items, $return = null)
    {
        if ($this->void_return_type) {
            throw new Method_Prophecy_Exception("The method \"{$this->method_name}\" has a void return type, and so cannot yield anything", $this);
        }
        if (!is_array($items)) {
            throw new InvalidArgumentException(sprintf('Expected array, but got %s.', gettype($items)));
        }
        $generator = function () use ($items, $return) {
            yield from $items;
            return $return;
        };
        return $this->will($generator);
    }
    /**
     * Sets return argument promise to the prophecy.
     *
     * @param int $index The zero-indexed number of the argument to return
     *
     * @see \Prophecy\Promise\ReturnArgumentPromise
     *
     * @return $this
     */
    public function will_return_argument($index = 0)
    {
        if ($this->void_return_type) {
            throw new Method_Prophecy_Exception("The method \"{$this->method_name}\" has a void return type", $this);
        }
        return $this->will(new Promise\Return_Argument_Promise($index));
    }
    /**
     * Sets throw promise to the prophecy.
     *
     * @see \Prophecy\Promise\ThrowPromise
     *
     * @param string|\Throwable $exception Exception class or instance
     *
     * @return $this
     *
     * @phpstan-param class-string<\Throwable>|\Throwable $exception
     */
    public function will_throw($exception)
    {
        return $this->will(new Promise\Throw_Promise($exception));
    }
    /**
     * Sets custom prediction to the prophecy.
     *
     * @param callable|Prediction\PredictionInterface $prediction
     *
     * @return $this
     *
     * @throws \Prophecy\Exception\InvalidArgumentException
     */
    public function should($prediction): static
    {
        if (is_callable($prediction)) {
            $prediction = new Prediction\Callback_Prediction($prediction);
        }
        if (!$prediction instanceof Prediction\Prediction_Interface) {
            throw new InvalidArgumentException(sprintf('Expected callable or instance of PredictionInterface, but got %s.', gettype($prediction)));
        }
        $this->bind_to_object_prophecy();
        $this->prediction = $prediction;
        return $this;
    }
    /**
     * Sets call prediction to the prophecy.
     *
     * @see \Prophecy\Prediction\CallPrediction
     *
     * @return $this
     */
    public function should_be_called()
    {
        return $this->should(new Prediction\Call_Prediction());
    }
    /**
     * Sets no calls prediction to the prophecy.
     *
     * @see \Prophecy\Prediction\NoCallsPrediction
     *
     * @return $this
     */
    public function should_not_be_called()
    {
        return $this->should(new Prediction\No_Calls_Prediction());
    }
    /**
     * Sets call times prediction to the prophecy.
     *
     * @see \Prophecy\Prediction\CallTimesPrediction
     *
     * @param int $count
     *
     * @return $this
     */
    public function should_be_called_times($count)
    {
        return $this->should(new Prediction\Call_Times_Prediction($count));
    }
    /**
     * Sets call times prediction to the prophecy.
     *
     * @see \Prophecy\Prediction\CallTimesPrediction
     *
     * @return $this
     */
    public function should_be_called_once()
    {
        return $this->should_be_called_times(1);
    }
    /**
     * Checks provided prediction immediately.
     *
     * @param callable|Prediction\PredictionInterface $prediction
     *
     * @return $this
     *
     * @throws \Prophecy\Exception\InvalidArgumentException
     * @throws PredictionException
     */
    public function should_have($prediction): static
    {
        if (is_callable($prediction)) {
            $prediction = new Prediction\Callback_Prediction($prediction);
        }
        if (!$prediction instanceof Prediction\Prediction_Interface) {
            throw new InvalidArgumentException(sprintf('Expected callable or instance of PredictionInterface, but got %s.', gettype($prediction)));
        }
        if (null === $this->promise && !$this->void_return_type) {
            $this->will_return();
        }
        $calls = $this->get_object_prophecy()->find_prophecy_method_calls($this->get_method_name(), $this->get_arguments_wildcard());
        try {
            $prediction->check($calls, $this->get_object_prophecy(), $this);
            $this->checked_predictions[] = $prediction;
        } catch (\Exception $e) {
            $this->checked_predictions[] = $prediction;
            throw $e;
        }
        return $this;
    }
    /**
     * Checks call prediction.
     *
     * @see \Prophecy\Prediction\CallPrediction
     *
     * @return $this
     *
     * @throws PredictionException
     */
    public function should_have_been_called()
    {
        return $this->should_have(new Prediction\Call_Prediction());
    }
    /**
     * Checks no calls prediction.
     *
     * @see \Prophecy\Prediction\NoCallsPrediction
     *
     * @return $this
     *
     * @throws PredictionException
     */
    public function should_not_have_been_called()
    {
        return $this->should_have(new Prediction\No_Calls_Prediction());
    }
    /**
     * Checks no calls prediction.
     *
     * @see \Prophecy\Prediction\NoCallsPrediction
     * @deprecated
     *
     * @return $this
     */
    public function should_not_been_called()
    {
        return $this->should_not_have_been_called();
    }
    /**
     * Checks call times prediction.
     *
     * @see \Prophecy\Prediction\CallTimesPrediction
     *
     * @param int $count
     *
     * @return $this
     */
    public function should_have_been_called_times($count)
    {
        return $this->should_have(new Prediction\Call_Times_Prediction($count));
    }
    /**
     * Checks call times prediction.
     *
     * @see \Prophecy\Prediction\CallTimesPrediction
     *
     * @return $this
     */
    public function should_have_been_called_once()
    {
        return $this->should_have_been_called_times(1);
    }
    /**
     * Checks currently registered [with should(...)] prediction.
     *
     *
     * @throws PredictionException
     */
    public function check_prediction(): void
    {
        if (null === $this->prediction) {
            return;
        }
        $this->should_have($this->prediction);
    }
    /**
     * Returns currently registered promise.
     *
     * @return null|Promise\PromiseInterface
     */
    public function get_promise()
    {
        return $this->promise;
    }
    /**
     * Returns currently registered prediction.
     *
     * @return null|Prediction\PredictionInterface
     */
    public function get_prediction()
    {
        return $this->prediction;
    }
    /**
     * Returns predictions that were checked on this object.
     *
     * @return list<Prediction\PredictionInterface>
     */
    public function get_checked_predictions()
    {
        return $this->checked_predictions;
    }
    /**
     * Returns object prophecy this method prophecy is tied to.
     *
     * @return ObjectProphecy<object>
     */
    public function get_object_prophecy()
    {
        return $this->object_prophecy;
    }
    /**
     * Returns method name.
     *
     * @return string
     */
    public function get_method_name()
    {
        return $this->method_name;
    }
    /**
     * Returns arguments wildcard.
     *
     * @return Argument\ArgumentsWildcard
     */
    public function get_arguments_wildcard()
    {
        return $this->arguments_wildcard;
    }
    /**
     * @return bool
     */
    public function has_return_void()
    {
        return $this->void_return_type;
    }
    private function bind_to_object_prophecy(): void
    {
        if ($this->bound) {
            return;
        }
        $this->get_object_prophecy()->add_method_prophecy($this);
        $this->bound = true;
    }
}