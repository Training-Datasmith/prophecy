<?php

declare(strict_types=1);

namespace spec\Prophecy\Doubler\Generator;

use PhpSpec\ObjectBehavior;
use Prophecy\Doubler\Generator\ClassCodeGenerator;
use Prophecy\Doubler\Generator\Node\ClassNode;

class ClassCreatorSpec extends ObjectBehavior
{
    public function let(ClassCodeGenerator $generator)
    {
        $this->beConstructedWith($generator);
    }

    public function it_evaluates_code_generated_by_ClassCodeGenerator($generator, ClassNode $class)
    {
        $generator->generate('stdClass', $class)->shouldBeCalled()->willReturn(
            'return 42;'
        );

        $this->create('stdClass', $class)->shouldReturn(42);
    }

    public function it_throws_an_exception_if_class_does_not_exist_after_evaluation($generator, ClassNode $class)
    {
        $generator->generate('CustomClass', $class)->shouldBeCalled()->willReturn(
            'return 42;'
        );

        $class->getParentClass()->willReturn('stdClass');
        $class->getInterfaces()->willReturn(['Interface1', 'Interface2']);

        $this->shouldThrow('Prophecy\Exception\Doubler\ClassCreatorException')
            ->duringCreate('CustomClass', $class);
    }
}
