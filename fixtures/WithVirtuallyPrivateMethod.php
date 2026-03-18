<?php

declare(strict_types=1);

namespace Fixtures\Prophecy;

class WithVirtuallyPrivateMethod
{
    public function __toString()
    {
        return '';
    }

    public function _getName()
    {
    }

    public function isAbstract()
    {
    }
}
