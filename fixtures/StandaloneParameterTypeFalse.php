<?php

declare(strict_types=1);

namespace Fixtures\Prophecy;

class StandaloneParameterTypeFalse
{
    public function method(false $arg)
    {
        return $arg;
    }
}
