<?php

declare(strict_types=1);

namespace Fixtures\Prophecy;

class NeverType
{
    public function doSomething(): never
    {
        throw new \RuntimeException();
    }
}
