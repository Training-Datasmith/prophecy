<?php

declare(strict_types=1);

namespace spec\Prophecy\Prediction;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument\ArgumentsWildcard;
use Prophecy\Call\Call;
use Prophecy\Prophecy\MethodProphecy;
use Prophecy\Prophecy\ObjectProphecy;

class NoCallsPredictionSpec extends ObjectBehavior
{
    public function it_is_prediction()
    {
        $this->shouldHaveType('Prophecy\Prediction\PredictionInterface');
    }

    public function it_does_nothing_if_there_is_no_calls_made(ObjectProphecy $object, MethodProphecy $method)
    {
        $this->check([], $object, $method)->shouldReturn(null);
    }

    public function it_throws_UnexpectedCallsException_if_calls_found(
        ObjectProphecy $object,
        MethodProphecy $method,
        Call $call,
        ArgumentsWildcard $arguments
    ) {
        $object->reveal()->willReturn(new \stdClass());
        $method->getObjectProphecy()->willReturn($object);
        $method->getMethodName()->willReturn('getName');
        $method->getArgumentsWildcard()->willReturn($arguments);
        $arguments->__toString()->willReturn('123');

        $call->getMethodName()->willReturn('getName');
        $call->getArguments()->willReturn([5, 4, 'three']);
        $call->getCallPlace()->willReturn('unknown');

        $this->shouldThrow('Prophecy\Exception\Prediction\UnexpectedCallsException')
            ->duringCheck([$call], $object, $method);
    }
}
