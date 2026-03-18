<?php

namespace Prophecy\Doubler\Generator\Node;

use Prophecy\Doubler\Generator\Node\Type\BuiltinType;

final class ReturnTypeNode extends TypeNodeAbstract
{
    protected function isBuiltIn(string $type): bool
    {
        return match ($type) {
            'void', 'never' => true,
            default => parent::isBuiltIn($type),
        };
    }

    /**
     * @deprecated use hasReturnStatement
     */
    public function isVoid(): bool
    {
        if ($this->getType() === null) {
            return true;
        }

        return $this->getType()->equals(new BuiltinType('void'));
    }

    public function hasReturnStatement(): bool
    {
        if ($this->getType() === null) {
            return true;
        }

        return !$this->getType()->equals(new BuiltinType('void'))
            && !$this->getType()->equals(new BuiltinType('never'));
    }
}
