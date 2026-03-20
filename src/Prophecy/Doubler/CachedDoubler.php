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
 * Cached class doubler.
 * Prevents mirroring/creation of the same structure twice.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Cached_Doubler extends Doubler
{
    /**
     * @var array<string, class-string>
     */
    private static array $classes = [];
    protected function create_double_class(?ReflectionClass $class, array $interfaces)
    {
        $class_id = $this->generate_class_id($class, $interfaces);
        return self::$classes[$class_id] ?? self::$classes[$class_id] = parent::create_double_class($class, $interfaces);
    }
    /**
     * @param ReflectionClass<object>|null $class
     * @param ReflectionClass<object>[]    $interfaces
     */
    private function generate_class_id(?ReflectionClass $class, array $interfaces): string
    {
        $parts = [];
        if (null !== $class) {
            $parts[] = $class->get_name();
        }
        foreach ($interfaces as $interface) {
            $parts[] = $interface->get_name();
        }
        foreach ($this->get_class_patches() as $patch) {
            $parts[] = $patch::class;
        }
        sort($parts);
        return md5(implode('', $parts));
    }
    public function reset_cache(): void
    {
        self::$classes = [];
    }
}