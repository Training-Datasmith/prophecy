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

use Prophecy\Doubler\Generator\Node\ArgumentNode;
use Prophecy\Doubler\Generator\Node\ClassNode;
use Prophecy\Doubler\Generator\Node\MethodNode;
use Prophecy\PhpDocumentor\ClassAndInterfaceTagRetriever;
use Prophecy\PhpDocumentor\MethodTagRetrieverInterface;

/**
 * Discover Magical API using "@method" PHPDoc format.
 *
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 * @author Kévin Dunglas <dunglas@gmail.com>
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class MagicCallPatch implements ClassPatchInterface
{
    public const MAGIC_METHODS_WITH_ARGUMENTS = ['__call', '__callStatic', '__get', '__isset', '__set', '__set_state', '__unserialize', '__unset'];

    public function __construct(private readonly ?MethodTagRetrieverInterface $tagRetriever = new ClassAndInterfaceTagRetriever())
    {
    }

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
     * Discover Magical API
     */
    public function apply(ClassNode $node): void
    {
        $types = array_filter($node->getInterfaces(), fn (string $interface) => !str_starts_with($interface, 'Prophecy\\'));
        $types[] = $node->getParentClass();

        foreach ($types as $type) {
            $reflectionClass = new \ReflectionClass($type);

            while ($reflectionClass) {
                $tagList = $this->tagRetriever->getTagList($reflectionClass);

                foreach ($tagList as $tag) {
                    $methodName = $tag->getMethodName();

                    if (empty($methodName)) {
                        continue;
                    }

                    if (!$reflectionClass->hasMethod($methodName)) {
                        $methodNode = new MethodNode($methodName);

                        // only magic methods can have a contract that needs to be enforced
                        if (in_array($methodName, self::MAGIC_METHODS_WITH_ARGUMENTS)) {
                            if (method_exists($tag, 'getParameters')) {
                                // Reflection Docblock 5.4.0+.
                                foreach ($tag->getParameters() as $argument) {
                                    $argumentNode = new ArgumentNode($argument->getName());
                                    $methodNode->addArgument($argumentNode);
                                }
                            } else {
                                // Reflection Docblock < 5.4.0.
                                foreach ($tag->getArguments() as $argument) {
                                    $argumentNode = new ArgumentNode($argument['name']);
                                    $methodNode->addArgument($argumentNode);
                                }
                            }
                        }

                        $methodNode->setStatic($tag->isStatic());
                        $node->addMethod($methodNode);
                    }
                }

                $reflectionClass = $reflectionClass->getParentClass();
            }
        }
    }

    /**
     * Returns patch priority, which determines when patch will be applied.
     *
     * @return integer Priority number (higher - earlier)
     */
    public function getPriority(): int
    {
        return 50;
    }
}
