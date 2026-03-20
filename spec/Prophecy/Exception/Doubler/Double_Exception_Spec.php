<?php

declare(strict_types=1);

namespace spec\Prophecy\Exception\Doubler;

use PhpSpec\ObjectBehavior;

class DoubleExceptionSpec extends ObjectBehavior
{
    public function it_is_a_double_exception()
    {
        $this->shouldBeAnInstanceOf('RuntimeException');
        $this->shouldBeAnInstanceOf('Prophecy\Exception\Doubler\DoublerException');
    }
}
