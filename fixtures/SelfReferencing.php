<?php

declare(strict_types=1);

namespace Fixtures\Prophecy;

interface SelfReferencing
{
    public function __invoke(self $self): self;
}
