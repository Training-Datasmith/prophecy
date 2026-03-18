<?php

declare(strict_types=1);

namespace spec\Prophecy\Exception\Prediction;

use PhpSpec\ObjectBehavior;
use Prophecy\Exception\Prediction\FailedPredictionException;
use Prophecy\Exception\Prediction\PredictionException;
use Prophecy\Prophecy\ObjectProphecy;

class AggregateExceptionSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('');
    }

    public function it_is_prediction_exception()
    {
        $this->shouldBeAnInstanceOf('RuntimeException');
        $this->shouldBeAnInstanceOf('Prophecy\Exception\Prediction\PredictionException');
    }

    public function it_can_store_objectProphecy_link(ObjectProphecy $object)
    {
        $this->setObjectProphecy($object);
        $this->getObjectProphecy()->shouldReturn($object);
    }

    public function it_should_not_have_exceptions_at_the_beginning()
    {
        $this->getExceptions()->shouldHaveCount(0);
    }

    public function it_should_append_exception_through_append_method()
    {
        $exception = new FailedPredictionException();

        $this->append($exception);

        $this->getExceptions()->shouldReturn([$exception]);
    }

    public function it_should_update_message_during_append()
    {
        $exception = new FailedPredictionException('Exception #1');

        $this->append($exception);

        $this->getMessage()->shouldReturn('Exception #1');
    }

    public function it_should_update_message_during_append_more_exceptions(
        PredictionException $exception1,
        PredictionException $exception2
    ) {
        $exception1 = new FailedPredictionException('Exception #1');
        $exception2 = new FailedPredictionException('Exception #2');

        $this->append($exception1);
        $this->append($exception2);
        $this->getMessage()->shouldReturn("Exception #1\nException #2");
    }
}
