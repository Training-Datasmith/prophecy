<?php

declare(strict_types=1);

namespace Prophecy\Doubler\Generator\Node\Type;

interface SimpleType extends TypeInterface
{
    public function getType(): string;
}
