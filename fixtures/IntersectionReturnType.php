<?php

declare(strict_types=1);

namespace Fixtures\Prophecy;

class IntersectionReturnType
{
    public function doSomething(): Bar&Baz
    {

    }
}
