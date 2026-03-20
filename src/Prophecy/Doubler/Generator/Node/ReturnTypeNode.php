<?php

declare (strict_types=1);
namespace Prophecy\Doubler\Generator\Node;

use Prophecy\Doubler\Generator\Node\Type\Builtin_Type;
final class Return_Type_Node extends Type_Node_Abstract
{
    protected function is_built_in(string $type): bool
    {
        return match ($type) {
            'void', 'never' => true,
            default => parent::is_built_in($type),
        };
    }
    /**
     * @deprecated use hasReturnStatement
     */
    public function is_void(): bool
    {
        if ($this->get_type() === null) {
            return true;
        }
        return $this->get_type()->equals(new Builtin_Type('void'));
    }
    public function has_return_statement(): bool
    {
        if ($this->get_type() === null) {
            return true;
        }
        return !$this->get_type()->equals(new Builtin_Type('void')) && !$this->get_type()->equals(new Builtin_Type('never'));
    }
}