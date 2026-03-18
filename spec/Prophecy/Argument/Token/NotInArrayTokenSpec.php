<?php

declare(strict_types=1);

namespace spec\Prophecy\Argument\Token;

use PhpSpec\ObjectBehavior;

class NotInArrayTokenSpec extends ObjectBehavior
{
    public function it_implements_TokenInterface()
    {
        $this->beConstructedWith([]);
        $this->shouldBeAnInstanceOf('Prophecy\Argument\Token\TokenInterface');
    }

    public function it_is_not_last()
    {
        $this->beConstructedWith([]);
        $this->shouldNotBeLast();
    }

    public function it_scores_8_if_argument_is_not_in_array()
    {
        $this->beConstructedWith([1, 2, 3]);
        $this->scoreArgument(5)->shouldReturn(8);
    }

    public function it_scores_false_if_argument_is_in_array()
    {
        $this->beConstructedWith([1, 2, 3]);
        $this->scoreArgument(2)->shouldReturn(false);
    }

    public function it_generates_array_in_string_format()
    {
        $this->beConstructedWith([1, 2, 3]);
        $this->__toString()->shouldBe('[1, 2, 3]');
    }

    public function it_generates_an_empty_array_as_string_when_token_is_empty()
    {
        $this->beConstructedWith([]);
        $this->__toString()->shouldBe('[]');
    }
}
