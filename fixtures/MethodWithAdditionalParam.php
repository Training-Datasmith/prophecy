<?php

declare(strict_types=1);

namespace Fixtures\Prophecy;

abstract class MethodWithAdditionalParam extends WithArguments implements Named
{
    abstract public function getName($name = null);

    public function methodWithoutTypeHints($arg, $arg2 = null)
    {
    }
}
