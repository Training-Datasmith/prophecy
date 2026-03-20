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
namespace Prophecy\Argument\Token;

use Prophecy\Comparator\Factory_Provider;
use Prophecy\Util\String_Util;
use Sebastian_Bergmann\Comparator\Comparison_Failure;
use Sebastian_Bergmann\Comparator\Factory as ComparatorFactory;
/**
 * Object state-checker token.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Object_State_Token implements Token_Interface
{
    private readonly \Prophecy\Util\String_Util $util;
    private readonly \Sebastian_Bergmann\Comparator\Factory $comparator_factory;
    /**
     * Initializes token.
     *
     * @param string $name
     * @param mixed  $value             Expected return value
     */
    public function __construct(private $name, private $value, ?String_Util $util = null, ?Comparator_Factory $comparator_factory = null)
    {
        $this->util = $util ?: new String_Util();
        $this->comparator_factory = $comparator_factory ?: Factory_Provider::get_instance();
    }
    /**
     * Scores 8 if argument is an object, which method returns expected value.
     *
     * @param mixed $argument
     */
    public function score_argument($argument): int|false
    {
        $method_callable = [$argument, $this->name];
        if (is_object($argument) && method_exists($argument, $this->name) && is_callable($method_callable)) {
            $actual = call_user_func($method_callable);
            $comparator = $this->comparator_factory->get_comparator_for($this->value, $actual);
            try {
                $comparator->assert_equals($this->value, $actual);
                return 8;
            } catch (Comparison_Failure) {
                return false;
            }
        }
        if (is_object($argument) && property_exists($argument, $this->name)) {
            return $argument->{$this->name} === $this->value ? 8 : false;
        }
        return false;
    }
    /**
     * Returns false.
     */
    public function is_last(): bool
    {
        return false;
    }
    /**
     * Returns string representation for token.
     */
    public function __toString(): string
    {
        return sprintf('state(%s(), %s)', $this->name, $this->util->stringify($this->value));
    }
}