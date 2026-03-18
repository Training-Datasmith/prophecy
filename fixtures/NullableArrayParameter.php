<?php

declare(strict_types=1);

namespace Fixtures\Prophecy;

class NullableArrayParameter
{
    public function iHaveNullableArrayParameterWithNotNullDefaultValue(?array $arr = [])
    {
    }
}
