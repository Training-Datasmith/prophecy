<?php

declare(strict_types=1);

namespace Fixtures\Prophecy;

class WithCallableArgument
{
    public function methodWithArgs(callable $arg_1, ?callable $arg_2 = null)
    {
    }
}
