<?php

declare(strict_types=1);

namespace Fixtures\Prophecy;

class StandaloneParameterTypeTrue
{
    public function method(true $arg)
    {
        return $arg;
    }
}
