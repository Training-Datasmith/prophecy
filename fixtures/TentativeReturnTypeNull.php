<?php

declare(strict_types=1);

namespace Fixtures\Prophecy;

class TentativeReturnTypeNull
{
    #[\ReturnTypeWillChange()]
    public function method(): ?string
    {
        return null;
    }
}
