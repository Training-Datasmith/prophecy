<?php

declare(strict_types=1);

namespace spec\Prophecy;

use PhpSpec\ObjectBehavior;

class ArgumentSpec extends ObjectBehavior
{
    public function it_has_a_shortcut_for_exact_argument_token()
    {
        $token = $this->exact(42);
        $token->shouldBeAnInstanceOf('Prophecy\Argument\Token\ExactValueToken');
        $token->getValue()->shouldReturn(42);
    }

    public function it_has_a_shortcut_for_any_argument_token()
    {
        $token = $this->any();
        $token->shouldBeAnInstanceOf('Prophecy\Argument\Token\AnyValueToken');
    }

    public function it_has_a_shortcut_for_multiple_arguments_token()
    {
        $token = $this->cetera();
        $token->shouldBeAnInstanceOf('Prophecy\Argument\Token\AnyValuesToken');
    }

    public function it_has_a_shortcut_for_type_token()
    {
        $token = $this->type('integer');
        $token->shouldBeAnInstanceOf('Prophecy\Argument\Token\TypeToken');
    }

    public function it_has_a_shortcut_for_callback_token()
    {
        $token = $this->that('get_class');
        $token->shouldBeAnInstanceOf('Prophecy\Argument\Token\CallbackToken');
    }

    public function it_supports_customizing_tostring_representation_for_callback_token()
    {
        $token = $this->that('get_class', 'MyCustomTestCase');
        $token->shouldBeAnInstanceOf('Prophecy\Argument\Token\CallbackToken');
        $token->__toString()->shouldReturn('MyCustomTestCase');
    }

    public function it_has_a_shortcut_for_object_state_token()
    {
        $token = $this->which('getName', 'everzet');
        $token->shouldBeAnInstanceOf('Prophecy\Argument\Token\ObjectStateToken');
    }

    public function it_has_a_shortcut_for_logical_and_token()
    {
        $token = $this->allOf('integer', 5);
        $token->shouldBeAnInstanceOf('Prophecy\Argument\Token\LogicalAndToken');
    }

    public function it_has_a_shortcut_for_array_count_token()
    {
        $token = $this->size(5);
        $token->shouldBeAnInstanceOf('Prophecy\Argument\Token\ArrayCountToken');
    }

    public function it_has_a_shortcut_for_array_entry_token()
    {
        $token = $this->withEntry('key', 'value');
        $token->shouldBeAnInstanceOf('Prophecy\Argument\Token\ArrayEntryToken');
    }

    public function it_has_a_shortcut_for_array_every_entry_token()
    {
        $token = $this->withEveryEntry('value');
        $token->shouldBeAnInstanceOf('Prophecy\Argument\Token\ArrayEveryEntryToken');
    }

    public function it_has_a_shortcut_for_identical_value_token()
    {
        $token = $this->is('value');
        $token->shouldBeAnInstanceOf('Prophecy\Argument\Token\IdenticalValueToken');
    }

    public function it_has_a_shortcut_for_array_entry_token_matching_any_key()
    {
        $token = $this->containing('value');
        $token->shouldBeAnInstanceOf('Prophecy\Argument\Token\ArrayEntryToken');
        $token->getKey()->shouldHaveType('Prophecy\Argument\Token\AnyValueToken');
    }

    public function it_has_a_shortcut_for_array_entry_token_matching_any_value()
    {
        $token = $this->withKey('key');
        $token->shouldBeAnInstanceOf('Prophecy\Argument\Token\ArrayEntryToken');
        $token->getValue()->shouldHaveType('Prophecy\Argument\Token\AnyValueToken');
    }

    public function it_has_a_shortcut_for_logical_not_token()
    {
        $token = $this->not('kagux');
        $token->shouldBeAnInstanceOf('Prophecy\Argument\Token\LogicalNotToken');
    }

    public function it_has_a_shortcut_for_string_contains_token()
    {
        $token = $this->containingString('string');
        $token->shouldBeAnInstanceOf('Prophecy\Argument\Token\StringContainsToken');
    }

    public function it_has_a_shortcut_for_approximate_token()
    {
        $token = $this->approximate(10);
        $token->shouldBeAnInstanceOf('Prophecy\Argument\Token\ApproximateValueToken');
    }

    public function it_has_a_shortcut_for_in_token()
    {
        $token = $this->in([1, 2, 3]);
        $token->shouldBeAnInstanceOf('Prophecy\Argument\Token\InArrayToken');
    }

    public function it_has_a_shortcut_for_not_in_token()
    {
        $token = $this->notIn([1, 2, 3]);
        $token->shouldBeAnInstanceOf('Prophecy\Argument\Token\NotInArrayToken');
    }
}
