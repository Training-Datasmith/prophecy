<?php

declare(strict_types=1);

namespace Fixtures\Prophecy;

class NullableReturnTypeTrue
{
    public function method(): ?true
    {
        return true;
    }
}
