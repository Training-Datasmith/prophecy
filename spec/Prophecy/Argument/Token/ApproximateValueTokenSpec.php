<?php

declare(strict_types=1);

namespace spec\Prophecy\Argument\Token;

use PhpSpec\ObjectBehavior;

class ApproximateValueTokenSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(10.12345678, 4);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Prophecy\Argument\Token\ApproximateValueToken');
    }

    public function it_implements_TokenInterface()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Argument\Token\TokenInterface');
    }

    public function it_is_not_last()
    {
        $this->shouldNotBeLast();
    }

    public function it_scores_10_if_rounded_argument_matches_rounded_value()
    {
        $this->scoreArgument(10.12345)->shouldReturn(10);
    }

    public function it_does_not_score_if_rounded_argument_does_not_match_rounded_value()
    {
        $this->scoreArgument(10.1234)->shouldReturn(false);
    }

    public function it_uses_a_default_precision_of_zero()
    {
        $this->beConstructedWith(10.7);
        $this->scoreArgument(11.4)->shouldReturn(10);
    }

    public function it_does_not_score_if_rounded_argument_is_not_numeric()
    {
        $this->scoreArgument('hello')->shouldReturn(false);
        $this->scoreArgument(new \stdClass())->shouldReturn(false);
        $this->scoreArgument(false)->shouldReturn(false);
    }

    public function it_has_simple_string_representation()
    {
        $this->__toString()->shouldBe('≅10.1235');
    }
}
