<?php

declare(strict_types=1);

namespace Fixtures\Prophecy;

class WithReferences
{
    public function methodWithReferenceArgument(&$arg_1, \ArrayAccess &$arg_2)
    {
    }
}
