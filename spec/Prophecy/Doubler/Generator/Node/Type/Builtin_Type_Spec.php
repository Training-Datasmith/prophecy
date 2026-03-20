<?php

declare(strict_types=1);

namespace spec\Prophecy\Doubler\Generator\Node\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Doubler\Generator\Node\Type\TypeInterface;

class BuiltinTypeSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith('string');
    }

    public function it_implements_type_interface(): void
    {
        $this->shouldImplement(TypeInterface::class);
    }

    public function it_is_stringable(): void
    {
        $this->beConstructedWith('int');
        $this->getType()->shouldReturn('int');
        $this->__toString()->shouldReturn('int');
    }
}
