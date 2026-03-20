<?php

declare(strict_types=1);

namespace Fixtures\Prophecy;

class DnfArgumentType
{
    public function doSomething((A&B)|C $foo)
    {

    }
}
