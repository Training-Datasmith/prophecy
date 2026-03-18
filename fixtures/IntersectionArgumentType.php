<?php

declare(strict_types=1);

namespace Fixtures\Prophecy;

class IntersectionArgumentType
{
    public function doSomething(Bar&Baz $foo)
    {

    }
}
