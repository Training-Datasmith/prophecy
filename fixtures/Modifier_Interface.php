<?php

declare(strict_types=1);

namespace Fixtures\Prophecy;

interface ModifierInterface
{
    public function isAbstract();

    public function getVisibility();
}
