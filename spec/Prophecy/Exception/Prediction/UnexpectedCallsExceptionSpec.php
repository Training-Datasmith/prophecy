<?php

declare(strict_types=1);

namespace spec\Prophecy\Exception\Prediction;

use PhpSpec\ObjectBehavior;
use Prophecy\Call\Call;
use Prophecy\Prophecy\MethodProphecy;
use Prophecy\Prophecy\ObjectProphecy;

class UnexpectedCallsExceptionSpec extends ObjectBehavior
{
    public function let(ObjectProphecy $objectProphecy, MethodProphecy $methodProphecy, Call $call1, Call $call2)
    {
        $methodProphecy->getObjectProphecy()->willReturn($objectProphecy);

        $this->beConstructedWith('message', $methodProphecy, [$call1, $call2]);
    }

    public function it_is_PredictionException()
    {
        $this->shouldHaveType('Prophecy\Exception\Prediction\PredictionException');
    }

    public function it_extends_MethodProphecyException()
    {
        $this->shouldHaveType('Prophecy\Exception\Prophecy\MethodProphecyException');
    }

    public function it_should_expose_calls_list_through_getter($call1, $call2)
    {
        $this->getCalls()->shouldReturn([$call1, $call2]);
    }
}
