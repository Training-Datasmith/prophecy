<?php

declare(strict_types=1);

namespace Fixtures\Prophecy;

class NullableParameterTypeTrue
{
    public function method(?true $arg)
    {
        return $arg;
    }
}
