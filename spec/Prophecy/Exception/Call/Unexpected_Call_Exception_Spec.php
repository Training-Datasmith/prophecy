<?php

declare(strict_types=1);

namespace spec\Prophecy\Exception\Call;

use PhpSpec\ObjectBehavior;
use Prophecy\Prophecy\ObjectProphecy;

class UnexpectedCallExceptionSpec extends ObjectBehavior
{
    public function let(ObjectProphecy $objectProphecy)
    {
        $this->beConstructedWith('msg', $objectProphecy, 'getName', ['arg1', 'arg2']);
    }

    public function it_is_prophecy_exception()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Exception\Prophecy\ObjectProphecyException');
    }

    public function it_exposes_method_name_through_getter()
    {
        $this->getMethodName()->shouldReturn('getName');
    }

    public function it_exposes_arguments_through_getter()
    {
        $this->getArguments()->shouldReturn(['arg1', 'arg2']);
    }
}
