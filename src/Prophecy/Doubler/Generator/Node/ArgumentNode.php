<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prophecy\Doubler\Generator\Node;

/**
 * Argument node.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class ArgumentNode
{
    /**
     * @var mixed
     */
    private $default;
    private bool $optional    = false;

    /**
     * @var bool
     */
    private $byReference = false;

    /**
     * @var bool
     */
    private $isVariadic  = false;

    private \Prophecy\Doubler\Generator\Node\ArgumentTypeNode $typeNode;

    /**
     * @param string $name
     */
    public function __construct(private $name)
    {
        $this->typeNode = new ArgumentTypeNode();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function setTypeNode(ArgumentTypeNode $typeNode): void
    {
        $this->typeNode = $typeNode;
    }

    public function getTypeNode(): ArgumentTypeNode
    {
        return $this->typeNode;
    }

    public function hasDefault(): bool
    {
        return $this->isOptional() && !$this->isVariadic();
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param mixed $default
     */
    public function setDefault($default = null): void
    {
        $this->optional = true;
        $this->default  = $default;
    }

    /**
     * @return bool
     */
    public function isOptional()
    {
        return $this->optional;
    }

    /**
     * @param bool $byReference
     */
    public function setAsPassedByReference($byReference = true): void
    {
        $this->byReference = $byReference;
    }

    /**
     * @return bool
     */
    public function isPassedByReference()
    {
        return $this->byReference;
    }

    /**
     * @param bool $isVariadic
     */
    public function setAsVariadic($isVariadic = true): void
    {
        $this->isVariadic = $isVariadic;
    }

    /**
     * @return bool
     */
    public function isVariadic()
    {
        return $this->isVariadic;
    }

    /**
     * @deprecated use getArgumentTypeNode instead
     */
    public function getTypeHint(): ?string
    {
        $type = $this->typeNode->getNonNullTypes() ? $this->typeNode->getNonNullTypes()[0] : null;

        return $type ? ltrim($type, '\\') : null;
    }

    /**
     * @deprecated use setArgumentTypeNode instead
     * @param string|null $typeHint
     */
    public function setTypeHint($typeHint = null): void
    {
        $this->typeNode = ($typeHint === null) ? new ArgumentTypeNode() : new ArgumentTypeNode($typeHint);
    }

    /**
     * @deprecated use getArgumentTypeNode instead
     */
    public function isNullable(): bool
    {
        return $this->typeNode->canUseNullShorthand();
    }

    /**
     * @deprecated use getArgumentTypeNode instead
     * @param bool $isNullable
     */
    public function setAsNullable($isNullable = true): void
    {
        $nonNullTypes = $this->typeNode->getNonNullTypes();
        $this->typeNode = $isNullable ? new ArgumentTypeNode('null', ...$nonNullTypes) : new ArgumentTypeNode(...$nonNullTypes);
    }
}
