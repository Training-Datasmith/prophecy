<?php

declare(strict_types=1);

namespace spec\Prophecy\Comparator;

use PhpSpec\ObjectBehavior;
use Prophecy\Prophet;
use SebastianBergmann\Comparator\Comparator;
use SebastianBergmann\Comparator\Factory;

class ProphecyComparatorSpec extends ObjectBehavior
{
    public function it_is_a_comparator()
    {
        $this->shouldHaveType(Comparator::class);
    }

    public function it_accepts_only_prophecy_objects()
    {
        $this->accepts(123, 321)->shouldReturn(false);
        $this->accepts('string', 'string')->shouldReturn(false);
        $this->accepts(false, true)->shouldReturn(false);
        $this->accepts(true, false)->shouldReturn(false);
        $this->accepts((object) [], (object) [])->shouldReturn(false);
        $this->accepts(function () {
        }, (object) [])->shouldReturn(false);
        $this->accepts(function () {
        }, function () {
        })->shouldReturn(false);

        $prophet = new Prophet();
        $prophecy = $prophet->prophesize('Prophecy\Prophecy\ObjectProphecy');

        $this->accepts($prophecy, $prophecy)->shouldReturn(true);
    }

    public function it_asserts_that_an_object_is_equal_to_its_revealed_prophecy()
    {
        $prophet = new Prophet();
        $prophecy = $prophet->prophesize('Prophecy\Prophecy\ObjectProphecy');
        $prophecy->__call('reveal', [])->willReturn(new \stdClass());

        $factory = new Factory();
        $factory->register($this->getWrappedObject());

        $this->shouldNotThrow()->duringAssertEquals($prophecy->reveal(), $prophecy);
    }
}
