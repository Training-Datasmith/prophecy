<?php

declare(strict_types=1);

namespace Prophecy\Doubler\Generator\Node\Type;

interface TypeInterface extends \Stringable
{
    public function equals(TypeInterface $givenType): bool;
}
