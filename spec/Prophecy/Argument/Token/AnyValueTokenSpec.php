<?php

declare(strict_types=1);

namespace spec\Prophecy\Argument\Token;

use PhpSpec\ObjectBehavior;

class AnyValueTokenSpec extends ObjectBehavior
{
    public function it_implements_TokenInterface()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Argument\Token\TokenInterface');
    }

    public function it_is_not_last()
    {
        $this->shouldNotBeLast();
    }

    public function its_string_representation_is_star()
    {
        $this->__toString()->shouldReturn('*');
    }

    public function it_scores_any_argument_as_3()
    {
        $this->scoreArgument(42)->shouldReturn(3);
    }
}
