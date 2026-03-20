<?php

declare(strict_types=1);

namespace spec\Prophecy\Doubler;

use PhpSpec\ObjectBehavior;
use Prophecy\Doubler\Doubler;
use Prophecy\Prophecy\ProphecySubjectInterface;

class LazyDoubleSpec extends ObjectBehavior
{
    public function let(Doubler $doubler)
    {
        $this->beConstructedWith($doubler);
    }

    public function it_returns_anonymous_double_instance_by_default($doubler, ProphecySubjectInterface $double)
    {
        $doubler->double(null, [])->willReturn($double);

        $this->getInstance()->shouldReturn($double);
    }

    public function it_returns_class_double_instance_if_set($doubler, ProphecySubjectInterface $double, \ReflectionClass $class)
    {
        $doubler->double($class, [])->willReturn($double);

        $this->setParentClass($class);

        $this->getInstance()->shouldReturn($double);
    }

    public function it_returns_same_double_instance_if_called_2_times(
        $doubler,
        ProphecySubjectInterface $double1,
        ProphecySubjectInterface $double2
    ) {
        $doubler->double(null, [])->willReturn($double1);
        $doubler->double(null, [])->willReturn($double2);

        $this->getInstance()->shouldReturn($double2);
        $this->getInstance()->shouldReturn($double2);
    }

    public function its_setParentClass_throws_ClassNotFoundException_if_class_not_found()
    {
        $this->shouldThrow('Prophecy\Exception\Doubler\ClassNotFoundException')
            ->duringSetParentClass('SomeUnexistingClass');
    }

    public function its_setParentClass_throws_exception_if_prophecy_is_already_created(
        $doubler,
        ProphecySubjectInterface $double
    ) {
        $doubler->double(null, [])->willReturn($double);

        $this->getInstance();

        $this->shouldThrow('Prophecy\Exception\Doubler\DoubleException')
            ->duringSetParentClass('stdClass');
    }

    public function its_addInterface_throws_InterfaceNotFoundException_if_no_interface_found()
    {
        $this->shouldThrow('Prophecy\Exception\Doubler\InterfaceNotFoundException')
            ->duringAddInterface('SomeUnexistingInterface');
    }

    public function its_addInterface_throws_exception_if_prophecy_is_already_created(
        $doubler,
        ProphecySubjectInterface $double
    ) {
        $doubler->double(null, [])->willReturn($double);

        $this->getInstance();

        $this->shouldThrow('Prophecy\Exception\Doubler\DoubleException')
            ->duringAddInterface('ArrayAccess');
    }
}
