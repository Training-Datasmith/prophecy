<?php

declare (strict_types=1);
namespace Prophecy\Doubler\Generator\Node\Type;

interface Type_Interface extends \Stringable
{
    public function equals(Type_Interface $given_type): bool;
}