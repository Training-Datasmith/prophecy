<?php

declare(strict_types=1);

namespace Fixtures\Prophecy;

class UnionArgumentTypeFalse
{
    public function method(false|\stdClass $arg)
    {

    }
}
