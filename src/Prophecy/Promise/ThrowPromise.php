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

use Doctrine\Instantiator\Instantiator;
use Prophecy\Exception\InvalidArgumentException;
use Prophecy\Prophecy\Method_Prophecy;
use Prophecy\Prophecy\Object_Prophecy;
use ReflectionClass;
/**
 * Throws predefined exception.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Throw_Promise implements Promise_Interface
{
    private $exception;
    private ?\Doctrine\Instantiator\Instantiator $instantiator = null;
    /**
     * Initializes promise.
     *
     * @param string|\Throwable $exception Exception class name or instance
     *
     * @throws \Prophecy\Exception\InvalidArgumentException
     *
     * @phpstan-param class-string<\Throwable>|\Throwable $exception
     */
    public function __construct($exception)
    {
        if (is_string($exception)) {
            if (!class_exists($exception) && !interface_exists($exception) || !$this->is_a_valid_throwable($exception)) {
                throw new InvalidArgumentException(sprintf('Exception / Throwable class or instance expected as argument to ThrowPromise, but got %s.', $exception));
            }
        } elseif (!$exception instanceof \Exception && !$exception instanceof \Throwable) {
            throw new InvalidArgumentException(sprintf('Exception / Throwable class or instance expected as argument to ThrowPromise, but got %s.', get_debug_type($exception)));
        }
        $this->exception = $exception;
    }
    public function execute(array $args, Object_Prophecy $object, Method_Prophecy $method): void
    {
        if (is_string($this->exception)) {
            $classname = $this->exception;
            $reflection = new ReflectionClass($classname);
            $constructor = $reflection->get_constructor();
            if ($constructor === null || $constructor->is_public() && 0 == $constructor->get_number_of_required_parameters()) {
                throw $reflection->new_instance();
            }
            if (!$this->instantiator) {
                $this->instantiator = new Instantiator();
            }
            throw $this->instantiator->instantiate($classname);
        }
        throw $this->exception;
    }
    private function is_a_valid_throwable(string $exception): bool
    {
        return is_a($exception, 'Exception', true) || is_a($exception, 'Throwable', true);
    }
}