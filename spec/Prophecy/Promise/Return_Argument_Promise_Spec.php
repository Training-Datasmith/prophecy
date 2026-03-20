<?php

declare(strict_types=1);

namespace spec\Prophecy\Promise;

use PhpSpec\ObjectBehavior;
use Prophecy\Prophecy\MethodProphecy;
use Prophecy\Prophecy\ObjectProphecy;

class ReturnArgumentPromiseSpec extends ObjectBehavior
{
    public function it_is_promise()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Promise\PromiseInterface');
    }

    public function it_should_return_first_argument_if_provided(ObjectProphecy $object, MethodProphecy $method)
    {
        $this->execute(['one', 'two'], $object, $method)->shouldReturn('one');
    }

    public function it_should_return_null_if_no_arguments_provided(ObjectProphecy $object, MethodProphecy $method)
    {
        $this->execute([], $object, $method)->shouldReturn(null);
    }

    public function it_should_return_nth_argument_if_provided(ObjectProphecy $object, MethodProphecy $method)
    {
        $this->beConstructedWith(1);
        $this->execute(['one', 'two'], $object, $method)->shouldReturn('two');
    }
}
