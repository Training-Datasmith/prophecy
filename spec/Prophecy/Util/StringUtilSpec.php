<?php

declare(strict_types=1);

namespace spec\Prophecy\Util;

use PhpSpec\ObjectBehavior;

class StringUtilSpec extends ObjectBehavior
{
    public function it_generates_proper_string_representation_for_integer()
    {
        $this->stringify(42)->shouldReturn('42');
    }

    public function it_generates_proper_string_representation_for_string()
    {
        $this->stringify('some string')->shouldReturn('"some string"');
    }

    public function it_generates_single_line_representation_for_multiline_string()
    {
        $this->stringify("some\nstring")->shouldReturn('"some\\nstring"');
    }

    public function it_generates_proper_string_representation_for_double()
    {
        $this->stringify(42.3)->shouldReturn('42.3');
    }

    public function it_generates_proper_string_representation_for_boolean_true()
    {
        $this->stringify(true)->shouldReturn('true');
    }

    public function it_generates_proper_string_representation_for_boolean_false()
    {
        $this->stringify(false)->shouldReturn('false');
    }

    public function it_generates_proper_string_representation_for_null()
    {
        $this->stringify(null)->shouldReturn('null');
    }

    public function it_generates_proper_string_representation_for_empty_array()
    {
        $this->stringify([])->shouldReturn('[]');
    }

    public function it_generates_proper_string_representation_for_array()
    {
        $this->stringify(['zet', 42])->shouldReturn('["zet", 42]');
    }

    public function it_generates_proper_string_representation_for_hash_containing_one_value()
    {
        $this->stringify(['ever' => 'zet'])->shouldReturn('["ever" => "zet"]');
    }

    public function it_generates_proper_string_representation_for_hash()
    {
        $this->stringify(['ever' => 'zet', 52 => 'hey', 'num' => 42])->shouldReturn(
            '["ever" => "zet", 52 => "hey", "num" => 42]'
        );
    }

    public function it_generates_proper_string_representation_for_resource()
    {
        $resource = fopen(__FILE__, 'r');
        $this->stringify($resource)->shouldReturn('stream:'.$resource);
    }

    public function it_generates_proper_string_representation_for_object(\stdClass $object)
    {
        $objHash = sprintf(
            '%s#%s',
            get_class($object->getWrappedObject()),
            spl_object_id($object->getWrappedObject())
        )." Object (\n    'objectProphecyClosure' => Closure#%s Object (\n        0 => Closure#%s Object\n    )\n)";

        $idRegexExpr = '[0-9]+';
        $this->stringify($object)->shouldMatch(sprintf('/^%s$/', sprintf(preg_quote("$objHash"), $idRegexExpr, $idRegexExpr)));
    }

    public function it_generates_proper_string_representation_for_object_without_exporting(\stdClass $object)
    {
        $objHash = sprintf(
            '%s#%s',
            get_class($object->getWrappedObject()),
            spl_object_id($object->getWrappedObject())
        );

        $this->stringify($object, false)->shouldReturn("$objHash");
    }
}
