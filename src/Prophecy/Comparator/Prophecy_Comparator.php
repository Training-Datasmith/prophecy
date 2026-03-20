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
namespace Prophecy\Comparator;

use Prophecy\Prophecy\Prophecy_Interface;
use Sebastian_Bergmann\Comparator\Comparator;
use Sebastian_Bergmann\Comparator\Factory;
/**
 * @final
 */
class Prophecy_Comparator extends Comparator
{
    /**
     * @param mixed $expected
     * @param mixed $actual
     */
    public function accepts($expected, $actual): bool
    {
        return \is_object($expected) && $actual instanceof Prophecy_Interface;
    }
    /**
     * @param mixed $expected
     * @param mixed $actual
     * @param float $delta
     * @param bool  $canonicalize
     * @param bool  $ignoreCase
     */
    public function assert_equals($expected, $actual, $delta = 0.0, $canonicalize = false, $ignore_case = false): void
    {
        \assert($actual instanceof Prophecy_Interface);
        $this->get_comparator_factory()->get_comparator_for($expected, $actual->reveal())->assert_equals($expected, $actual->reveal(), $delta, $canonicalize, $ignore_case);
    }
    private function get_comparator_factory(): Factory
    {
        // sebastianbergmann/comparator 5+
        // @phpstan-ignore function.alreadyNarrowedType
        if (\method_exists($this, 'factory')) {
            return $this->factory();
        }
        // sebastianbergmann/comparator <5
        // @phpstan-ignore property.private
        return $this->factory;
    }
}