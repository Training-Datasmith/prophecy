<?php

declare(strict_types=1);

namespace Prophecy\Doubler\ClassPatch;

use Prophecy\Doubler\Generator\Node\ClassNode;
use Prophecy\Exception\Doubler\ClassCreatorException;

class ThrowablePatch implements ClassPatchInterface
{
    /**
     * Checks if patch supports specific class node.
     */
    public function supports(ClassNode $node): bool
    {
        return $this->implementsAThrowableInterface($node) && $this->doesNotExtendAThrowableClass($node);
    }

    private function implementsAThrowableInterface(ClassNode $node): bool
    {
        foreach ($node->getInterfaces() as $type) {
            if (is_a($type, 'Throwable', true)) {
                return true;
            }
        }

        return false;
    }

    private function doesNotExtendAThrowableClass(ClassNode $node): bool
    {
        return !is_a($node->getParentClass(), 'Throwable', true);
    }

    /**
     * Applies patch to the specific class node.
     *
     *
     */
    public function apply(ClassNode $node): void
    {
        $this->checkItCanBeDoubled($node);
        $this->setParentClassToException($node);
    }

    private function checkItCanBeDoubled(ClassNode $node): void
    {
        $className = $node->getParentClass();
        if ($className !== 'stdClass') {
            throw new ClassCreatorException(
                sprintf(
                    'Cannot double concrete class %s as well as implement Traversable',
                    $className
                ),
                $node
            );
        }
    }

    private function setParentClassToException(ClassNode $node): void
    {
        $node->setParentClass('Exception');

        $node->removeMethod('getMessage');
        $node->removeMethod('getCode');
        $node->removeMethod('getFile');
        $node->removeMethod('getLine');
        $node->removeMethod('getTrace');
        $node->removeMethod('getPrevious');
        $node->removeMethod('getNext');
        $node->removeMethod('getTraceAsString');
    }

    /**
     * Returns patch priority, which determines when patch will be applied.
     *
     * @return int Priority number (higher - earlier)
     */
    public function getPriority(): int
    {
        return 100;
    }
}
