<?php

declare(strict_types=1);

namespace Fixtures\Prophecy;

interface ReturningFinalClass
{
    public function doSomething(): FinalClass;
}
