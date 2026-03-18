<?php

declare(strict_types=1);

namespace Fixtures\Prophecy;

class NullableReturnTypeFalse
{
    public function method(): ?false
    {
        return false;
    }
}
