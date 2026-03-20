<?php

declare(strict_types=1);

namespace spec\Prophecy\Exception\Doubler;

use PhpSpec\ObjectBehavior;

class InterfaceNotFoundExceptionSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('msg', 'CustomInterface');
    }

    public function it_extends_ClassNotFoundException()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Exception\Doubler\ClassNotFoundException');
    }

    public function its_getClassname_returns_classname()
    {
        $this->getClassname()->shouldReturn('CustomInterface');
    }
}
