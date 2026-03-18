<?php

declare(strict_types=1);

namespace Fixtures\Prophecy;

class DnfReturnType
{
    public function doSomething(): (A&B)|C
    {

    }
}
