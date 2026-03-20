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
 * Exact value token.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Exact_Value_Token implements Token_Interface
{
    private ?string $string = null;
    private readonly \Prophecy\Util\String_Util $util;
    private readonly \Sebastian_Bergmann\Comparator\Factory $comparator_factory;
    /**
     * Initializes token.
     *
     * @param mixed $value
     */
    public function __construct(private $value, ?String_Util $util = null, ?Comparator_Factory $comparator_factory = null)
    {
        $this->util = $util ?: new String_Util();
        $this->comparator_factory = $comparator_factory ?: Factory_Provider::get_instance();
    }
    /**
     * Scores 10 if argument matches preset value.
     *
     * @param mixed $argument
     *
     * @return false|int
     */
    public function score_argument($argument): int|false
    {
        if (is_object($argument) && is_object($this->value)) {
            $comparator = $this->comparator_factory->get_comparator_for($argument, $this->value);
            try {
                $comparator->assert_equals($argument, $this->value);
                return 10;
            } catch (Comparison_Failure) {
                return false;
            }
        }
        // If either one is an object it should be castable to a string
        if (is_object($argument) xor is_object($this->value)) {
            if (is_object($argument) && !method_exists($argument, '__toString')) {
                return false;
            }
            if (is_object($this->value) && !method_exists($this->value, '__toString')) {
                return false;
            }
            if (is_numeric($argument) xor is_numeric($this->value)) {
                return strval($argument) == strval($this->value) ? 10 : false;
            }
        } elseif (is_numeric($argument) && is_numeric($this->value)) {
            // noop
        } elseif (gettype($argument) !== gettype($this->value)) {
            return false;
        }
        return $argument == $this->value ? 10 : false;
    }
    /**
     * Returns preset value against which token checks arguments.
     *
     * @return mixed
     */
    public function get_value()
    {
        return $this->value;
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
        if (null === $this->string) {
            $this->string = sprintf('exact(%s)', $this->util->stringify($this->value));
        }
        return $this->string;
    }
}