<?php

declare(strict_types=1);

namespace spec\Prophecy\Prophecy;

use PhpSpec\ObjectBehavior;
use Prophecy\Prophecy\ProphecyInterface;

class RevealerSpec extends ObjectBehavior
{
    public function it_is_revealer()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Prophecy\RevealerInterface');
    }

    public function it_reveals_single_instance_of_ProphecyInterface(ProphecyInterface $prophecy, \stdClass $object)
    {
        $prophecy->reveal()->willReturn($object);

        $this->reveal($prophecy)->shouldReturn($object);
    }

    public function it_reveals_instances_of_ProphecyInterface_inside_array(
        ProphecyInterface $prophecy1,
        ProphecyInterface $prophecy2,
        \stdClass $object1,
        \stdClass $object2
    ) {
        $prophecy1->reveal()->willReturn($object1);
        $prophecy2->reveal()->willReturn($object2);

        $this->reveal([
            ['item' => $prophecy2],
            $prophecy1,
        ])->shouldReturn([
            ['item' => $object2],
            $object1,
        ]);
    }

    public function it_does_not_touch_non_prophecy_interface()
    {
        $this->reveal(42)->shouldReturn(42);
    }
}
