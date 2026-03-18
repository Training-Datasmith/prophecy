<?php

declare(strict_types=1);

namespace spec\Prophecy\Argument\Token;

use PhpSpec\ObjectBehavior;

class ObjectStateTokenSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('getName', 'stdClass');
    }

    public function it_implements_TokenInterface()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Argument\Token\TokenInterface');
    }

    public function it_is_not_last()
    {
        $this->shouldNotBeLast();
    }

    public function it_scores_8_if_argument_object_has_specific_method_state(\ReflectionClass $reflection)
    {
        $reflection->getName()->willReturn('stdClass');

        $this->scoreArgument($reflection)->shouldReturn(8);
    }

    public function it_scores_8_if_argument_object_has_specific_property_state(\stdClass $class)
    {
        $class->getName = 'stdClass';

        $this->scoreArgument($class)->shouldReturn(8);
    }

    public function it_does_not_score_if_argument_method_state_does_not_match()
    {
        $value = new ObjectStateTokenFixtureB('ABC');
        $value2 = new ObjectStateTokenFixtureB('CBA');

        $this->beConstructedWith('getSelf', $value);
        $this->scoreArgument($value2)->shouldReturn(false);
    }

    public function it_does_not_score_if_argument_property_state_does_not_match(\stdClass $class)
    {
        $class->getName = 'SplFileInfo';

        $this->scoreArgument($class)->shouldReturn(false);
    }

    public function it_does_not_score_if_argument_object_does_not_have_method_or_property(ObjectStateTokenFixtureA $class)
    {
        $this->scoreArgument($class)->shouldReturn(false);
    }

    public function it_does_not_score_if_argument_is_not_object()
    {
        $this->scoreArgument(42)->shouldReturn(false);
    }

    public function it_has_simple_string_representation()
    {
        $this->__toString()->shouldReturn('state(getName(), "stdClass")');
    }
}

class ObjectStateTokenFixtureA
{
    public $errors;
}

class ObjectStateTokenFixtureB extends ObjectStateTokenFixtureA
{
    public $errors;
    public $value = null;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getSelf()
    {
        return $this;
    }
}
