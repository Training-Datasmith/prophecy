<?php

declare(strict_types=1);

namespace spec\Prophecy\Prediction;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Argument\ArgumentsWildcard;
use Prophecy\Call\Call;
use Prophecy\Prophecy\MethodProphecy;
use Prophecy\Prophecy\ObjectProphecy;

class CallTimesPredictionSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(2);
    }

    public function it_is_prediction()
    {
        $this->shouldHaveType('Prophecy\Prediction\PredictionInterface');
    }

    public function it_does_nothing_if_there_were_exact_amount_of_calls_being_made(
        ObjectProphecy $object,
        MethodProphecy $method,
        Call $call1,
        Call $call2
    ) {
        $this->check([$call1, $call2], $object, $method)->shouldReturn(null);
    }

    public function it_throws_UnexpectedCallsCountException_if_calls_found(
        ObjectProphecy $object,
        MethodProphecy $method,
        Call $call,
        ArgumentsWildcard $arguments
    ) {
        $object->reveal()->willReturn(new \stdClass());
        $object->findProphecyMethodCalls('getName', Argument::any())->willReturn([]);
        $method->getObjectProphecy()->willReturn($object);
        $method->getMethodName()->willReturn('getName');
        $method->getArgumentsWildcard()->willReturn($arguments);
        $arguments->__toString()->willReturn('123');

        $call->getMethodName()->willReturn('getName');
        $call->getArguments()->willReturn([5, 4, 'three']);
        $call->getCallPlace()->willReturn('unknown');

        $this->shouldThrow('Prophecy\Exception\Prediction\UnexpectedCallsCountException')
            ->duringCheck([$call], $object, $method);
    }
}
