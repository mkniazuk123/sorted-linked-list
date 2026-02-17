<?php

namespace MKniazuk\SortedLinkedList\Tests;

use MKniazuk\SortedLinkedList\SortedListFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(SortedListFactory::class)]
class SortedListFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        // Arrange:
        /** @var SampleItem[] $items */
        $items = [
            new SampleItem(100),
            new SampleItem(50),
            new SampleItem(150),
        ];

        // Act:
        $list = SortedListFactory::create(
            $items,
            fn (SampleItem $a, SampleItem $b) => $a->id <=> $b->id
        );

        // Assert:
        $this->assertSame(
            [
                $items[1],
                $items[0],
                $items[2],
            ],
            $list->toArray()
        );
    }

    public function testOfIntegers(): void
    {
        $list = SortedListFactory::ofIntegers([3, 1, 2]);

        $this->assertSame([1, 2, 3], $list->toArray());
    }

    public function testOfStrings(): void
    {
        $list = SortedListFactory::ofStrings(['banana', 'apple', 'cherry']);

        $this->assertSame(['apple', 'banana', 'cherry'], $list->toArray());
    }
}
