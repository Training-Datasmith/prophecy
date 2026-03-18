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

namespace Prophecy\Doubler\Generator\Node;

use Prophecy\Exception\InvalidArgumentException;

/**
 * Method node.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class MethodNode
{
    /**
     * @phpstan-var 'public'|'private'|'protected'
     */
    private string $visibility = 'public';
    private bool $static = false;
    private bool $returnsReference = false;

    private \Prophecy\Doubler\Generator\Node\ReturnTypeNode $returnTypeNode;

    /**
     * @var list<ArgumentNode>
     */
    private array $arguments = [];

    // Used to accept an optional third argument with the deprecated Prophecy\Doubler\Generator\TypeHintReference so careful when adding a new argument in a minor version.
    /**
     * @param string      $name
     * @param string|null $code
     */
    public function __construct(private $name, private $code = null)
    {
        $this->returnTypeNode = new ReturnTypeNode();
    }

    /**
     * @return string
     *
     * @phpstan-return 'public'|'private'|'protected'
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * @param string $visibility
     */
    public function setVisibility($visibility): void
    {
        $visibility = strtolower($visibility);

        if (!\in_array($visibility, ['public', 'private', 'protected'], true)) {
            throw new InvalidArgumentException(sprintf(
                '`%s` method visibility is not supported.',
                $visibility
            ));
        }

        $this->visibility = $visibility;
    }

    /**
     * @return bool
     */
    public function isStatic()
    {
        return $this->static;
    }

    /**
     * @param bool $static
     */
    public function setStatic($static = true): void
    {
        $this->static = (bool) $static;
    }

    /**
     * @return bool
     */
    public function returnsReference()
    {
        return $this->returnsReference;
    }

    public function setReturnsReference(): void
    {
        $this->returnsReference = true;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function addArgument(ArgumentNode $argument): void
    {
        $this->arguments[] = $argument;
    }

    /**
     * @return list<ArgumentNode>
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @deprecated use getReturnTypeNode instead
     */
    public function hasReturnType(): bool
    {
        return (bool) $this->returnTypeNode->getNonNullTypes();
    }

    public function setReturnTypeNode(ReturnTypeNode $returnTypeNode): void
    {
        $this->returnTypeNode = $returnTypeNode;
    }

    /**
     * @deprecated use setReturnTypeNode instead
     * @param string $type
     */
    public function setReturnType($type = null): void
    {
        $this->returnTypeNode = ($type === '' || $type === null) ? new ReturnTypeNode() : new ReturnTypeNode($type);
    }

    /**
     * @deprecated use setReturnTypeNode instead
     * @param bool $bool
     */
    public function setNullableReturnType($bool = true): void
    {
        if ($bool) {
            $this->returnTypeNode = new ReturnTypeNode('null', ...$this->returnTypeNode->getTypes());
        } else {
            $this->returnTypeNode = new ReturnTypeNode(...$this->returnTypeNode->getNonNullTypes());
        }
    }

    /**
     * @deprecated use getReturnTypeNode instead
     * @return string|null
     */
    public function getReturnType()
    {
        if ($types = $this->returnTypeNode->getNonNullTypes()) {
            return $types[0];
        }

        return null;
    }

    public function getReturnTypeNode(): ReturnTypeNode
    {
        return $this->returnTypeNode;
    }

    /**
     * @deprecated use getReturnTypeNode instead
     */
    public function hasNullableReturnType(): bool
    {
        return $this->returnTypeNode->isNullable();
    }

    /**
     * @param string $code
     */
    public function setCode($code): void
    {
        $this->code = $code;
    }

    public function getCode(): string
    {
        if ($this->returnsReference) {
            return "throw new \Prophecy\Exception\Doubler\ReturnByReferenceException('Returning by reference not supported', get_class(\$this), '{$this->name}');";
        }

        return (string) $this->code;
    }

    public function useParentCode(): void
    {
        $this->code = sprintf(
            'return parent::%s(%s);',
            $this->getName(),
            implode(
                ', ',
                array_map($this->generateArgument(...), $this->arguments)
            )
        );
    }

    private function generateArgument(ArgumentNode $arg): string
    {
        $argument = '$'.$arg->getName();

        if ($arg->isVariadic()) {
            return '...'.$argument;
        }

        return $argument;
    }
}
