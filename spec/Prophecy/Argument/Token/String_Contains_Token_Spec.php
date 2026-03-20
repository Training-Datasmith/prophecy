<?php

declare(strict_types=1);

namespace spec\Prophecy\Argument\Token;

use PhpSpec\ObjectBehavior;

class StringContainsTokenSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('a substring');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Prophecy\Argument\Token\StringContainsToken');
    }

    public function it_implements_TokenInterface()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Argument\Token\TokenInterface');
    }

    public function it_holds_value()
    {
        $this->getValue()->shouldReturn('a substring');
    }

    public function it_is_not_last()
    {
        $this->shouldNotBeLast();
    }

    public function it_scores_6_if_the_argument_contains_the_value()
    {
        $this->scoreArgument('Argument containing a substring')->shouldReturn(6);
    }

    public function it_does_not_score_if_the_argument_does_not_contain_the_value()
    {
        $this->scoreArgument('Argument will not match')->shouldReturn(false);
    }

    public function it_does_not_score_if_the_argument_is_not_a_string()
    {
        $this->scoreArgument(['a substring', 'other value'])->shouldReturn(false);
    }

    public function its_string_representation_shows_substring()
    {
        $this->__toString()->shouldReturn('contains("a substring")');
    }
}
