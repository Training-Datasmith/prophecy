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
namespace Prophecy\Doubler;

use ReflectionClass;
/**
 * Name generator.
 * Generates classname for double.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Name_Generator
{
    private static int $counter = 1;
    /**
     * Generates name.
     *
     * @param ReflectionClass<object>|null $class
     * @param ReflectionClass<object>[]    $interfaces
     */
    public function name(?ReflectionClass $class, array $interfaces): string
    {
        $parts = [];
        if (null !== $class) {
            $parts[] = $class->get_name();
        } else {
            foreach ($interfaces as $interface) {
                $parts[] = $interface->get_short_name();
            }
        }
        if (!count($parts)) {
            $parts[] = 'stdClass';
        }
        return sprintf('Double\%s\P%d', implode('\\', $parts), self::$counter++);
    }
}