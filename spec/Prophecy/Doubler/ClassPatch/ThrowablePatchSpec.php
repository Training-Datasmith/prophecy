<?php

declare(strict_types=1);

namespace spec\Prophecy\Doubler\ClassPatch;

use PhpSpec\ObjectBehavior;
use Prophecy\Doubler\Generator\Node\ClassNode;

class ThrowablePatchSpec extends ObjectBehavior
{
    public function it_is_a_patch()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Doubler\ClassPatch\ClassPatchInterface');
    }

    public function it_does_not_support_class_that_does_not_implement_throwable(ClassNode $node)
    {
        $node->getInterfaces()->willReturn([]);
        $node->getParentClass()->willReturn('stdClass');

        $this->supports($node)->shouldReturn(false);
    }

    public function it_supports_class_that_extends_not_throwable_class(ClassNode $node)
    {
        $node->getInterfaces()->willReturn(['Throwable']);
        $node->getParentClass()->willReturn('stdClass');

        $this->supports($node)->shouldReturn(true);
    }

    public function it_does_not_support_class_that_already_extends_a_throwable_class(ClassNode $node)
    {
        $node->getInterfaces()->willReturn(['Throwable']);
        $node->getParentClass()->willReturn('InvalidArgumentException');

        $this->supports($node)->shouldReturn(false);
    }

    public function it_supports_class_implementing_interface_that_extends_throwable(ClassNode $node)
    {
        $node->getInterfaces()->willReturn(['Fixtures\Prophecy\ThrowableInterface']);
        $node->getParentClass()->willReturn('stdClass');

        $this->supports($node)->shouldReturn(true);
    }

    public function it_sets_the_parent_class_to_exception(ClassNode $node)
    {
        $node->getParentClass()->willReturn('stdClass');

        $node->setParentClass('Exception')->shouldBeCalled();

        $node->removeMethod('getMessage')->shouldBeCalled();
        $node->removeMethod('getCode')->shouldBeCalled();
        $node->removeMethod('getFile')->shouldBeCalled();
        $node->removeMethod('getLine')->shouldBeCalled();
        $node->removeMethod('getTrace')->shouldBeCalled();
        $node->removeMethod('getPrevious')->shouldBeCalled();
        $node->removeMethod('getNext')->shouldBeCalled();
        $node->removeMethod('getTraceAsString')->shouldBeCalled();

        $this->apply($node);
    }

    public function it_throws_error_when_trying_to_double_concrete_class_and_throwable_interface(ClassNode $node)
    {
        $node->getParentClass()->willReturn('ArrayObject');

        $this->shouldThrow('Prophecy\Exception\Doubler\ClassCreatorException')->duringApply($node);
    }
}
