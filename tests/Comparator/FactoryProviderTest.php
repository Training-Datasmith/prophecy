<?php

declare(strict_types=1);

namespace Tests\Prophecy\Comparator;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\Comparator\ClosureComparator;
use Prophecy\Comparator\FactoryProvider;

class FactoryProviderTest extends TestCase
{
    #[Test]
    public function it_should_have_ClosureComparator_registered(): void
    {
        $comparator = FactoryProvider::getInstance()->getComparatorFor(function () {
        }, function () {
        });

        $this->assertInstanceOf(ClosureComparator::class, $comparator);
    }
}
