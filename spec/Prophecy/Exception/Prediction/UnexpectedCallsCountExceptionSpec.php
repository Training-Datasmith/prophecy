<?php

declare(strict_types=1);

namespace spec\Prophecy\Exception\Prediction;

use PhpSpec\ObjectBehavior;
use Prophecy\Call\Call;
use Prophecy\Prophecy\MethodProphecy;
use Prophecy\Prophecy\ObjectProphecy;

class UnexpectedCallsCountExceptionSpec extends ObjectBehavior
{
    public function let(ObjectProphecy $objectProphecy, MethodProphecy $methodProphecy, Call $call1, Call $call2)
    {
        $methodProphecy->getObjectProphecy()->willReturn($objectProphecy);

        $this->beConstructedWith('message', $methodProphecy, 5, [$call1, $call2]);
    }

    public function it_extends_UnexpectedCallsException()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Exception\Prediction\UnexpectedCallsException');
    }

    public function it_should_expose_expectedCount_through_getter()
    {
        $this->getExpectedCount()->shouldReturn(5);
    }
}
