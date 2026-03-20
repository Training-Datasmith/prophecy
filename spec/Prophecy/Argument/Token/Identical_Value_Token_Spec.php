<?php

declare(strict_types=1);

namespace spec\Prophecy\Argument\Token;

use PhpSpec\ObjectBehavior;

class IdenticalValueTokenSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(42);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Prophecy\Argument\Token\IdenticalValueToken');
    }

    public function it_scores_11_if_string_value_is_identical_to_argument()
    {
        $this->beConstructedWith('foo');
        $this->scoreArgument('foo')->shouldReturn(11);
    }

    public function it_scores_11_if_boolean_value_is_identical_to_argument()
    {
        $this->beConstructedWith(false);
        $this->scoreArgument(false)->shouldReturn(11);
    }

    public function it_scores_11_if_integer_value_is_identical_to_argument()
    {
        $this->beConstructedWith(31);
        $this->scoreArgument(31)->shouldReturn(11);
    }

    public function it_scores_11_if_float_value_is_identical_to_argument()
    {
        $this->beConstructedWith(31.12);
        $this->scoreArgument(31.12)->shouldReturn(11);
    }

    public function it_scores_11_if_array_value_is_identical_to_argument()
    {
        $this->beConstructedWith(['foo' => 'bar']);
        $this->scoreArgument(['foo' => 'bar'])->shouldReturn(11);
    }

    public function it_scores_11_if_object_value_is_identical_to_argument()
    {
        $object = new \stdClass();

        $this->beConstructedWith($object);
        $this->scoreArgument($object)->shouldReturn(11);
    }

    public function it_scores_false_if_value_is_not_identical_to_argument()
    {
        $this->beConstructedWith(new \stdClass());
        $this->scoreArgument('foo')->shouldReturn(false);
    }

    public function it_scores_false_if_object_value_is_not_the_same_instance_than_argument()
    {
        $this->beConstructedWith(new \stdClass());
        $this->scoreArgument(new \stdClass())->shouldReturn(false);
    }

    public function it_scores_false_if_integer_value_is_not_identical_to_boolean_argument()
    {
        $this->beConstructedWith(1);
        $this->scoreArgument(true)->shouldReturn(false);
    }

    public function it_is_not_last()
    {
        $this->shouldNotBeLast();
    }

    public function it_generates_proper_string_representation_for_integer()
    {
        $this->beConstructedWith(42);
        $this->__toString()->shouldReturn('identical(42)');
    }

    public function it_generates_proper_string_representation_for_string()
    {
        $this->beConstructedWith('some string');
        $this->__toString()->shouldReturn('identical("some string")');
    }

    public function it_generates_single_line_representation_for_multiline_string()
    {
        $this->beConstructedWith("some\nstring");
        $this->__toString()->shouldReturn('identical("some\\nstring")');
    }

    public function it_generates_proper_string_representation_for_double()
    {
        $this->beConstructedWith(42.3);
        $this->__toString()->shouldReturn('identical(42.3)');
    }

    public function it_generates_proper_string_representation_for_boolean_true()
    {
        $this->beConstructedWith(true);
        $this->__toString()->shouldReturn('identical(true)');
    }

    public function it_generates_proper_string_representation_for_boolean_false()
    {
        $this->beConstructedWith(false);
        $this->__toString()->shouldReturn('identical(false)');
    }

    public function it_generates_proper_string_representation_for_null()
    {
        $this->beConstructedWith(null);
        $this->__toString()->shouldReturn('identical(null)');
    }

    public function it_generates_proper_string_representation_for_empty_array()
    {
        $this->beConstructedWith([]);
        $this->__toString()->shouldReturn('identical([])');
    }

    public function it_generates_proper_string_representation_for_array()
    {
        $this->beConstructedWith(['zet', 42]);
        $this->__toString()->shouldReturn('identical(["zet", 42])');
    }

    public function it_generates_proper_string_representation_for_resource()
    {
        $resource = fopen(__FILE__, 'r');
        $this->beConstructedWith($resource);
        $this->__toString()->shouldReturn('identical(stream:'.$resource.')');
    }

    public function it_generates_proper_string_representation_for_object($object)
    {
        $objHash = sprintf(
            'identical(%s#%s',
            get_class($object->getWrappedObject()),
            spl_object_id($object->getWrappedObject())
        )." Object (\n    'objectProphecyClosure' => Closure#%s Object (\n        0 => Closure#%s Object\n    )\n))";

        $this->beConstructedWith($object);

        $idRegexExpr = '[0-9]+';
        $this->__toString()->shouldMatch(sprintf('/^%s$/', sprintf(preg_quote("$objHash"), $idRegexExpr, $idRegexExpr)));
    }
}
