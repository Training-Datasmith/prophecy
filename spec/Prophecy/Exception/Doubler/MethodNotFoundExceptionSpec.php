<?php

declare(strict_types=1);

namespace spec\Prophecy\Exception\Doubler;

use PhpSpec\ObjectBehavior;

class MethodNotFoundExceptionSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('', 'User', 'getName', [1, 2, 3]);
    }

    public function it_is_DoubleException()
    {
        $this->shouldHaveType('Prophecy\Exception\Doubler\DoubleException');
    }

    public function it_has_MethodName()
    {
        $this->getMethodName()->shouldReturn('getName');
    }

    public function it_has_classnamej()
    {
        $this->getClassname()->shouldReturn('User');
    }

    public function it_has_an_arguments_list()
    {
        $this->getArguments()->shouldReturn([1, 2, 3]);
    }

    public function it_has_a_default_null_argument_list()
    {
        $this->beConstructedWith('', 'User', 'getName');
        $this->getArguments()->shouldReturn(null);
    }
}
