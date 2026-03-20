<?php

declare(strict_types=1);

namespace Fixtures\Prophecy;

class StandaloneReturnTypeTrue
{
    public function method(): true
    {
        return true;
    }
}
