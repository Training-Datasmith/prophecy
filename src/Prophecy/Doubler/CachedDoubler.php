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

namespace Prophecy\Doubler;

use ReflectionClass;

/**
 * Cached class doubler.
 * Prevents mirroring/creation of the same structure twice.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class CachedDoubler extends Doubler
{
    /**
     * @var array<string, class-string>
     */
    private static array $classes = [];

    protected function createDoubleClass(?ReflectionClass $class, array $interfaces)
    {
        $classId = $this->generateClassId($class, $interfaces);

        return self::$classes[$classId] ?? self::$classes[$classId] = parent::createDoubleClass($class, $interfaces);
    }

    /**
     * @param ReflectionClass<object>|null $class
     * @param ReflectionClass<object>[]    $interfaces
     */
    private function generateClassId(?ReflectionClass $class, array $interfaces): string
    {
        $parts = [];
        if (null !== $class) {
            $parts[] = $class->getName();
        }
        foreach ($interfaces as $interface) {
            $parts[] = $interface->getName();
        }
        foreach ($this->getClassPatches() as $patch) {
            $parts[] = $patch::class;
        }
        sort($parts);

        return md5(implode('', $parts));
    }

    public function resetCache(): void
    {
        self::$classes = [];
    }
}
