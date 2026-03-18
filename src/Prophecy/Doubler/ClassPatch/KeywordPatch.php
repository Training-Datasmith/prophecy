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

namespace Prophecy\Doubler\ClassPatch;

use Prophecy\Doubler\Generator\Node\ClassNode;

/**
 * Remove method functionality from the double which will clash with php keywords.
 *
 * @author Milan Magudia <milan@magudia.com>
 */
class KeywordPatch implements ClassPatchInterface
{
    /**
     * Support any class
     *
     *
     */
    public function supports(ClassNode $node): bool
    {
        return true;
    }

    /**
     * Remove methods that clash with php keywords
     */
    public function apply(ClassNode $node): void
    {
        $methodNames = array_keys($node->getMethods());
        $methodsToRemove = array_intersect($methodNames, $this->getKeywords());
        foreach ($methodsToRemove as $methodName) {
            $node->removeMethod($methodName);
        }
    }

    /**
     * Returns patch priority, which determines when patch will be applied.
     *
     * @return int Priority number (higher - earlier)
     */
    public function getPriority(): int
    {
        return 49;
    }

    /**
     * Returns array of php keywords.
     *
     * @return list<string>
     */
    private function getKeywords(): array
    {
        return ['__halt_compiler'];
    }
}
