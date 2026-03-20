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

use Prophecy\Argument\Arguments_Wildcard;
use Prophecy\Call\Call;
use Prophecy\Call\Call_Center;
use Prophecy\Comparator\Factory_Provider;
use Prophecy\Doubler\Lazy_Double;
use Prophecy\Exception\Prediction\Aggregate_Exception;
use Prophecy\Exception\Prediction\Prediction_Exception;
use Prophecy\Exception\Prophecy\Object_Prophecy_Exception;
use Sebastian_Bergmann\Comparator\Comparison_Failure;
use Sebastian_Bergmann\Comparator\Factory as ComparatorFactory;
/**
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * @template-covariant T of object
 * @template-implements ProphecyInterface<T>
 */
class Object_Prophecy implements Prophecy_Interface
{
    private readonly \Prophecy\Call\Call_Center $call_center;
    private readonly \Prophecy\Prophecy\Revealer_Interface $revealer;
    private readonly \Sebastian_Bergmann\Comparator\Factory $comparator_factory;
    /**
     * @var array<string, list<MethodProphecy>>
     */
    private array $method_prophecies = [];
    /**
     * @param LazyDouble<T> $lazyDouble
     */
    public function __construct(private readonly Lazy_Double $lazy_double, ?Call_Center $call_center = null, ?Revealer_Interface $revealer = null, ?Comparator_Factory $comparator_factory = null)
    {
        $this->call_center = $call_center ?: new Call_Center();
        $this->revealer = $revealer ?: new Revealer();
        $this->comparator_factory = $comparator_factory ?: Factory_Provider::get_instance();
    }
    /**
     * Forces double to extend specific class.
     *
     * @param string $class
     *
     * @return $this
     *
     * @template U of object
     * @phpstan-param class-string<U> $class
     * @phpstan-this-out static<T&U>
     */
    public function will_extend($class): static
    {
        $this->lazy_double->set_parent_class($class);
        return $this;
    }
    /**
     * Forces double to implement specific interface.
     *
     * @param string $interface
     *
     * @return $this
     *
     * @template U of object
     * @phpstan-param class-string<U> $interface
     * @phpstan-this-out static<T&U>
     */
    public function will_implement($interface): static
    {
        $this->lazy_double->add_interface($interface);
        return $this;
    }
    /**
     * Sets constructor arguments.
     *
     * @param array<mixed> $arguments
     *
     * @return $this
     */
    public function will_be_constructed_with(?array $arguments = null): static
    {
        $this->lazy_double->set_arguments($arguments);
        return $this;
    }
    /**
     * Reveals double.
     *
     * @return object
     *
     * @throws \Prophecy\Exception\Prophecy\ObjectProphecyException If double doesn't implement needed interface
     *
     * @phpstan-return T
     */
    public function reveal()
    {
        $double = $this->lazy_double->get_instance();
        if (!$double instanceof Prophecy_Subject_Interface) {
            throw new Object_Prophecy_Exception("Generated double must implement ProphecySubjectInterface, but it does not.\n" . 'It seems you have wrongly configured doubler without required ClassPatch.', $this);
        }
        $double->set_prophecy($this);
        return $double;
    }
    /**
     * Adds method prophecy to object prophecy.
     *
     *
     */
    public function add_method_prophecy(Method_Prophecy $method_prophecy): void
    {
        $method_name = strtolower($method_prophecy->get_method_name());
        if (!isset($this->method_prophecies[$method_name])) {
            $this->method_prophecies[$method_name] = [];
        }
        $this->method_prophecies[$method_name][] = $method_prophecy;
    }
    /**
     * Returns either all or related to single method prophecies.
     *
     * @param null|string $methodName
     *
     * @return MethodProphecy[]|array<string, MethodProphecy[]>
     *
     * @phpstan-return ($methodName is string ? list<MethodProphecy> : array<string, list<MethodProphecy>>)
     */
    public function get_method_prophecies($method_name = null)
    {
        if (null === $method_name) {
            return $this->method_prophecies;
        }
        $method_name = strtolower($method_name);
        if (!isset($this->method_prophecies[$method_name])) {
            return [];
        }
        return $this->method_prophecies[$method_name];
    }
    /**
     * Makes specific method call.
     *
     * @param string $methodName
     * @param array<mixed> $arguments
     *
     * @return mixed
     */
    public function make_prophecy_method_call($method_name, array $arguments)
    {
        $arguments = $this->revealer->reveal($arguments);
        \assert(\is_array($arguments));
        $return = $this->call_center->make_call($this, $method_name, $arguments);
        return $this->revealer->reveal($return);
    }
    /**
     * Finds calls by method name & arguments wildcard.
     *
     * @param string            $methodName
     *
     * @return list<Call>
     */
    public function find_prophecy_method_calls($method_name, Arguments_Wildcard $wildcard): array
    {
        return $this->call_center->find_calls($method_name, $wildcard);
    }
    /**
     * Checks that registered method predictions do not fail.
     *
     *
     * @throws \Prophecy\Exception\Prediction\AggregateException If any of registered predictions fail
     * @throws \Prophecy\Exception\Call\UnexpectedCallException
     */
    public function check_prophecy_methods_predictions(): void
    {
        $exception = new Aggregate_Exception(sprintf("%s:\n", $this->reveal()::class));
        $exception->set_object_prophecy($this);
        $this->call_center->check_unexpected_calls();
        foreach ($this->method_prophecies as $prophecies) {
            foreach ($prophecies as $prophecy) {
                try {
                    $prophecy->check_prediction();
                } catch (Prediction_Exception $e) {
                    $exception->append($e);
                }
            }
        }
        if (count($exception->get_exceptions())) {
            throw $exception;
        }
    }
    /**
     * Creates new method prophecy using specified method name and arguments.
     *
     * @param array<mixed> $arguments
     * @return MethodProphecy
     */
    public function __call(string $method_name, array $arguments)
    {
        $arguments = $this->revealer->reveal($arguments);
        \assert(\is_array($arguments));
        $arguments = new Arguments_Wildcard($arguments);
        foreach ($this->get_method_prophecies($method_name) as $prophecy) {
            $arguments_wildcard = $prophecy->get_arguments_wildcard();
            $comparator = $this->comparator_factory->get_comparator_for($arguments_wildcard, $arguments);
            try {
                $comparator->assert_equals($arguments_wildcard, $arguments);
                return $prophecy;
            } catch (Comparison_Failure) {
            }
        }
        return new Method_Prophecy($this, $method_name, $arguments);
    }
    /**
     * Tries to get property value from double.
     *
     *
     */
    public function __get(string $name): mixed
    {
        return $this->reveal()->{$name};
    }
    /**
     * Tries to set property value to double.
     *
     *
     * @return void
     */
    public function __set(string $name, mixed $value)
    {
        $this->reveal()->{$name} = $this->revealer->reveal($value);
    }
}