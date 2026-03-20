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
namespace Prophecy\Doubler\Class_Patch;

use Prophecy\Doubler\Generator\Node\Class_Node;
/**
 * Remove method functionality from the double which will clash with php keywords.
 *
 * @author Milan Magudia <milan@magudia.com>
 */
class Keyword_Patch implements Class_Patch_Interface
{
    /**
     * Support any class
     *
     *
     */
    public function supports(Class_Node $node): bool
    {
        return true;
    }
    /**
     * Remove methods that clash with php keywords
     */
    public function apply(Class_Node $node): void
    {
        $method_names = array_keys($node->get_methods());
        $methods_to_remove = array_intersect($method_names, $this->get_keywords());
        foreach ($methods_to_remove as $method_name) {
            $node->remove_method($method_name);
        }
    }
    /**
     * Returns patch priority, which determines when patch will be applied.
     *
     * @return int Priority number (higher - earlier)
     */
    public function get_priority(): int
    {
        return 49;
    }
    /**
     * Returns array of php keywords.
     *
     * @return list<string>
     */
    private function get_keywords(): array
    {
        return ['__halt_compiler'];
    }
}