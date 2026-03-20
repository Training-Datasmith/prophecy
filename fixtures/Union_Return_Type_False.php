<?php

declare(strict_types=1);

namespace Fixtures\Prophecy;

class UnionReturnTypeFalse
{
    public function method(): false|\stdClass
    {

    }
}
