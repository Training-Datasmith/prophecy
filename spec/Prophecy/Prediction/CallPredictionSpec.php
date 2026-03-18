<?php

declare(strict_types=1);

namespace spec\Prophecy\Prediction;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Argument\ArgumentsWildcard;
use Prophecy\Call\Call;
use Prophecy\Prophecy\MethodProphecy;
use Prophecy\Prophecy\ObjectProphecy;

class CallPredictionSpec extends ObjectBehavior
{
    public function it_is_prediction()
    {
        $this->shouldHaveType('Prophecy\Prediction\PredictionInterface');
    }

    public function it_does_nothing_if_there_is_more_than_one_call_been_made(
        ObjectProphecy $object,
        MethodProphecy $method,
        Call $call
    ) {
        $this->check([$call], $object, $method)->shouldReturn(null);
    }

    public function it_throws_NoCallsException_if_no_calls_found(
        ObjectProphecy $object,
        MethodProphecy $method,
        ArgumentsWildcard $arguments
    ) {
        $method->getObjectProphecy()->willReturn($object);
        $method->getMethodName()->willReturn('getName');
        $method->getArgumentsWildcard()->willReturn($arguments);
        $arguments->__toString()->willReturn('123');
        $object->reveal()->willReturn(new \stdClass());
        $object->findProphecyMethodCalls('getName', Argument::any())->willReturn([]);

        $this->shouldThrow('Prophecy\Exception\Prediction\NoCallsException')
            ->duringCheck([], $object, $method);
    }
}
