<?php

declare(strict_types=1);

namespace Fixtures\Prophecy;

class StandaloneParameterTypeNull
{
    public function method(null $arg)
    {
        return $arg;
    }
}
