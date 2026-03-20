<?php

declare(strict_types=1);

namespace spec\Prophecy\Exception\Doubler;

use PhpSpec\ObjectBehavior;

class ClassMirrorExceptionSpec extends ObjectBehavior
{
    public function let(\ReflectionClass $class)
    {
        $this->beConstructedWith('', $class);
    }

    public function it_is_a_prophecy_exception()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Exception\Exception');
        $this->shouldBeAnInstanceOf('Prophecy\Exception\Doubler\DoublerException');
    }

    public function it_contains_a_reflected_class_link($class)
    {
        $this->getReflectedClass()->shouldReturn($class);
    }
}
