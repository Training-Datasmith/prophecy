<?php

declare(strict_types=1);

namespace spec\Prophecy\Exception\Prophecy;

use PhpSpec\ObjectBehavior;
use Prophecy\Prophecy\ObjectProphecy;

class ObjectProphecyExceptionSpec extends ObjectBehavior
{
    public function let(ObjectProphecy $objectProphecy)
    {
        $this->beConstructedWith('message', $objectProphecy);
    }

    public function it_should_be_a_prophecy_exception()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Exception\Prophecy\ProphecyException');
    }

    public function it_holds_double_reference($objectProphecy)
    {
        $this->getObjectProphecy()->shouldReturn($objectProphecy);
    }
}
