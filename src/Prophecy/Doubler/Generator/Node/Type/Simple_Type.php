<?php

declare (strict_types=1);
namespace Prophecy\Doubler\Generator\Node\Type;

interface Simple_Type extends Type_Interface
{
    public function get_type(): string;
}