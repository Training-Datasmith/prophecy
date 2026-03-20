<?php

declare(strict_types=1);

namespace spec\Prophecy\Exception\Doubler;

use PhpSpec\ObjectBehavior;

class MethodNotExtendableExceptionSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('', 'User', 'getName');
    }

    public function it_is_DoubleException()
    {
        $this->shouldHaveType('Prophecy\Exception\Doubler\DoubleException');
    }

    public function it_has_MethodName()
    {
        $this->getMethodName()->shouldReturn('getName');
    }

    public function it_has_classname()
    {
        $this->getClassName()->shouldReturn('User');
    }
}
