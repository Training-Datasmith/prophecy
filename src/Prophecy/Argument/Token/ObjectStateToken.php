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

namespace Prophecy\Argument\Token;

use Prophecy\Comparator\FactoryProvider;
use Prophecy\Util\StringUtil;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Comparator\Factory as ComparatorFactory;

/**
 * Object state-checker token.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class ObjectStateToken implements TokenInterface
{
    private readonly \Prophecy\Util\StringUtil $util;
    private readonly \SebastianBergmann\Comparator\Factory $comparatorFactory;

    /**
     * Initializes token.
     *
     * @param string $name
     * @param mixed  $value             Expected return value
     */
    public function __construct(
        private $name,
        private $value,
        ?StringUtil $util = null,
        ?ComparatorFactory $comparatorFactory = null
    ) {
        $this->util  = $util ?: new StringUtil();

        $this->comparatorFactory = $comparatorFactory ?: FactoryProvider::getInstance();
    }

    /**
     * Scores 8 if argument is an object, which method returns expected value.
     *
     * @param mixed $argument
     */
    public function scoreArgument($argument): int|false
    {
        $methodCallable = [$argument, $this->name];
        if (is_object($argument) && method_exists($argument, $this->name) && is_callable($methodCallable)) {
            $actual = call_user_func($methodCallable);

            $comparator = $this->comparatorFactory->getComparatorFor(
                $this->value,
                $actual
            );

            try {
                $comparator->assertEquals($this->value, $actual);
                return 8;
            } catch (ComparisonFailure) {
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
    public function isLast(): bool
    {
        return false;
    }

    /**
     * Returns string representation for token.
     */
    public function __toString(): string
    {
        return sprintf(
            'state(%s(), %s)',
            $this->name,
            $this->util->stringify($this->value)
        );
    }
}
