<?php

declare(strict_types=1);

namespace spec\Prophecy\Comparator;

use PhpSpec\Exception\Example\SkippingException;
use PhpSpec\ObjectBehavior;
use Prophecy\Comparator\Factory;
use SebastianBergmann\Comparator\Factory as BaseFactory;

class FactorySpec extends ObjectBehavior
{
    public function let()
    {
        $ref = new \ReflectionClass(BaseFactory::class);

        if ($ref->isFinal()) {
            throw new SkippingException(sprintf('The deprecated "%s" class cannot be used with sebastian/comparator 5+.', Factory::class));
        }
    }

    public function it_extends_Sebastian_Comparator_Factory()
    {
        $this->shouldHaveType('SebastianBergmann\Comparator\Factory');
    }

    public function it_should_have_ClosureComparator_registered()
    {
        $comparator = $this->getInstance()->getComparatorFor(function () {
        }, function () {
        });
        $comparator->shouldHaveType('Prophecy\Comparator\ClosureComparator');
    }
}
