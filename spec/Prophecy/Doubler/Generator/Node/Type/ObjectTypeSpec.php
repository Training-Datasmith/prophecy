<?php

declare(strict_types=1);

namespace spec\Prophecy\Doubler\Generator\Node\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Doubler\Generator\Node\Type\TypeInterface;
use stdClass;

class ObjectTypeSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith(stdClass::class);
    }

    public function it_implements_type_interface(): void
    {
        $this->shouldImplement(TypeInterface::class);
    }

    public function it_is_stringable(): void
    {
        $this->beConstructedWith('stdClass');
        $this->getType()->shouldReturn('stdClass');
        $this->__toString()->shouldReturn('\stdClass');
    }
}
