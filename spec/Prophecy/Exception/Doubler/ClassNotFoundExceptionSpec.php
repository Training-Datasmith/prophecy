<?php

declare(strict_types=1);

namespace spec\Prophecy\Exception\Doubler;

use PhpSpec\ObjectBehavior;

class ClassNotFoundExceptionSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('msg', 'CustomClass');
    }

    public function it_is_a_prophecy_exception()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Exception\Exception');
        $this->shouldBeAnInstanceOf('Prophecy\Exception\Doubler\DoubleException');
    }

    public function its_getClassname_returns_classname()
    {
        $this->getClassname()->shouldReturn('CustomClass');
    }
}
