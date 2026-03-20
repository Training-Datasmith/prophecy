<?php

declare(strict_types=1);

namespace spec\Prophecy\Exception\Prophecy;

use PhpSpec\ObjectBehavior;
use Prophecy\Prophecy\MethodProphecy;
use Prophecy\Prophecy\ObjectProphecy;

class MethodProphecyExceptionSpec extends ObjectBehavior
{
    public function let(ObjectProphecy $objectProphecy, MethodProphecy $methodProphecy)
    {
        $methodProphecy->getObjectProphecy()->willReturn($objectProphecy);

        $this->beConstructedWith('message', $methodProphecy);
    }

    public function it_extends_DoubleException()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Exception\Prophecy\ObjectProphecyException');
    }

    public function it_holds_a_stub_reference($methodProphecy)
    {
        $this->getMethodProphecy()->shouldReturn($methodProphecy);
    }
}
