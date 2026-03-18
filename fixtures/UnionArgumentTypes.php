<?php

declare(strict_types=1);

namespace Fixtures\Prophecy;

class UnionArgumentTypes
{
    public function doSomething(bool|\stdClass $arg)
    {

    }
}
