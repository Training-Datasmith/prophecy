<?php

declare(strict_types=1);

namespace spec\Prophecy\Promise;

use PhpSpec\ObjectBehavior;
use Prophecy\Prophecy\MethodProphecy;
use Prophecy\Prophecy\ObjectProphecy;

class ReturnPromiseSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith([42]);
    }

    public function it_is_promise()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Promise\PromiseInterface');
    }

    public function it_returns_value_it_was_constructed_with(ObjectProphecy $object, MethodProphecy $method)
    {
        $this->execute([], $object, $method)->shouldReturn(42);
    }

    public function it_always_returns_last_value_left_in_the_return_values(ObjectProphecy $object, MethodProphecy $method)
    {
        $this->execute([], $object, $method)->shouldReturn(42);
        $this->execute([], $object, $method)->shouldReturn(42);
    }

    public function it_consequently_returns_multiple_values_it_was_constructed_with(
        ObjectProphecy $object,
        MethodProphecy $method
    ) {
        $this->beConstructedWith([42, 24, 12]);

        $this->execute([], $object, $method)->shouldReturn(42);
        $this->execute([], $object, $method)->shouldReturn(24);
        $this->execute([], $object, $method)->shouldReturn(12);
    }

    public function it_returns_null_if_constructed_with_empty_array(ObjectProphecy $object, MethodProphecy $method)
    {
        $this->beConstructedWith([]);

        $this->execute([], $object, $method)->shouldReturn(null);
    }
}
