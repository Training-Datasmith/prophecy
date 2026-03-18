<?php

declare(strict_types=1);

namespace Fixtures\Prophecy;

class NullableParameterTypeFalse
{
    public function method(?false $arg)
    {
        return $arg;
    }
}
