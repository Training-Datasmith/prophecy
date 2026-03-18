<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prophecy\PhpDocumentor;

use phpDocumentor\Reflection\DocBlock\Tags\Method;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 *
 * @internal
 */
final class ClassAndInterfaceTagRetriever implements MethodTagRetrieverInterface
{
    private \Prophecy\PhpDocumentor\MethodTagRetrieverInterface|\Prophecy\PhpDocumentor\ClassTagRetriever $classRetriever;

    public function __construct(?MethodTagRetrieverInterface $classRetriever = null)
    {
        if (null !== $classRetriever) {
            $this->classRetriever = $classRetriever;

            return;
        }

        $this->classRetriever = new ClassTagRetriever();
    }

    public function getTagList(\ReflectionClass $reflectionClass): array
    {
        return array_merge(
            $this->classRetriever->getTagList($reflectionClass),
            $this->getInterfacesTagList($reflectionClass)
        );
    }

    /**
     * @param \ReflectionClass<object> $reflectionClass
     *
     * @return list<Method>
     */
    private function getInterfacesTagList(\ReflectionClass $reflectionClass): array
    {
        $interfaces = $reflectionClass->getInterfaces();
        $tagList = [];

        foreach ($interfaces as $interface) {
            $tagList = array_merge($tagList, $this->classRetriever->getTagList($interface));
        }

        return $tagList;
    }
}
