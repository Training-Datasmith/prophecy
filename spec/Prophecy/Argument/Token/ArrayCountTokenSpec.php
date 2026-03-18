<?php

declare(strict_types=1);

namespace spec\Prophecy\Argument\Token;

use PhpSpec\ObjectBehavior;

class ArrayCountTokenSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(2);
    }

    public function it_implements_TokenInterface()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Argument\Token\TokenInterface');
    }

    public function it_is_not_last()
    {
        $this->shouldNotBeLast();
    }

    public function it_scores_6_if_argument_array_has_proper_count()
    {
        $this->scoreArgument([1,2])->shouldReturn(6);
    }

    public function it_scores_6_if_argument_countable_object_has_proper_count(\Countable $countable)
    {
        $countable->count()->willReturn(2);
        $this->scoreArgument($countable)->shouldReturn(6);
    }

    public function it_does_not_score_if_argument_is_neither_array_nor_countable_object()
    {
        $this->scoreArgument('string')->shouldBe(false);
        $this->scoreArgument(5)->shouldBe(false);
        $this->scoreArgument(new \stdClass())->shouldBe(false);
    }

    public function it_does_not_score_if_argument_array_has_wrong_count()
    {
        $this->scoreArgument([1])->shouldReturn(false);
    }

    public function it_does_not_score_if_argument_countable_object_has_wrong_count(\Countable $countable)
    {
        $countable->count()->willReturn(3);
        $this->scoreArgument($countable)->shouldReturn(false);
    }

    public function it_has_simple_string_representation()
    {
        $this->__toString()->shouldBe('count(2)');
    }

}
