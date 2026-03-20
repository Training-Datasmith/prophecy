<?php

declare(strict_types=1);

namespace spec\Prophecy\Exception\Prediction;

use PhpSpec\ObjectBehavior;
use Prophecy\Prophecy\MethodProphecy;
use Prophecy\Prophecy\ObjectProphecy;

class NoCallsExceptionSpec extends ObjectBehavior
{
    public function let(ObjectProphecy $objectProphecy, MethodProphecy $methodProphecy)
    {
        $methodProphecy->getObjectProphecy()->willReturn($objectProphecy);

        $this->beConstructedWith('message', $methodProphecy);
    }

    public function it_is_PredictionException()
    {
        $this->shouldHaveType('Prophecy\Exception\Prediction\PredictionException');
    }

    public function it_extends_MethodProphecyException()
    {
        $this->shouldHaveType('Prophecy\Exception\Prophecy\MethodProphecyException');
    }
}
