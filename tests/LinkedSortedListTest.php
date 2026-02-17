<?php

namespace MKniazuk\SortedLinkedList\Tests;

use MKniazuk\SortedLinkedList\LinkedSortedList;
use MKniazuk\SortedLinkedList\NoSuchElementException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(LinkedSortedList::class)]
class LinkedSortedListTest extends TestCase
{
    /**
     * @var LinkedSortedList<TestItem>
     */
    private LinkedSortedList $list;

    public function testSize(): void
    {
        $this->createEmpty();
        $this->assertListCount(0);
        $this->assertIsEmpty();

        $this->createWithValues([1, 2, 3]);
        $this->assertListCount(3);
        $this->assertIsNotEmpty();
    }

    public function testCreateEmpty(): void
    {
        $this->createEmpty();

        $this->assertListCount(0);
        $this->assertIsEmpty();
    }

    public function testCreateWithItems(): void
    {
        $this->createWithValues([1, 2, 3]);

        $this->assertListCount(3);
        $this->assertIsNotEmpty();
    }

    public function testItemsAreSorted(): void
    {
        $this->createWithValues([3, 1, 2]);

        $this->assertListHasValues([1, 2, 3]);
    }

    public function testHeadAndTail(): void
    {
        $this->createWithValues([1, 2, 3]);

        $this->assertListHeadValue(1);
        $this->assertListTailValue(3);
    }

    public function testContains(): void
    {
        $this->createWithValues([1, 2]);

        $this->assertListContainsValue(1);
        $this->assertListContainsValue(2);
        $this->assertListDoesNotContainValue(3);
        $this->assertListDoesNotContainValue(4);
    }

    public function testExists(): void
    {
        $this->createWithValues([1, 2]);

        $this->assertListExists(fn (TestItem $item) => 1 === $item->value);
        $this->assertListExists(fn (TestItem $item) => 2 === $item->value);
        $this->assertListDoesNotExist(fn (TestItem $item) => 3 === $item->value);
        $this->assertListDoesNotExist(fn (TestItem $item) => 4 === $item->value);
    }

    public function testIndexOf(): void
    {
        // Arrange:
        $this->createWithValues([1, 2, 2, 3]);

        // Assert:
        $this->assertIndexOfValueIs(1, 0);
        $this->assertIndexOfValueIs(2, 1);
        $this->assertIndexOfValueIs(3, 3);
        $this->assertIndexOfValueIsNull(4);
    }

    public function testCountOccurrences(): void
    {
        $this->createWithValues([1, 2, 2, 3]);

        $this->assertListValueOccurrencesCount(1, 1);
        $this->assertListValueOccurrencesCount(2, 2);
        $this->assertListValueOccurrencesCount(3, 1);
        $this->assertListValueOccurrencesCount(4, 0);
    }

    public function testAdd(): void
    {
        // Arrange:
        $this->createEmpty();

        // Act:
        $this->addValue(2);
        $this->addValue(1);
        $this->addValue(3);

        // Assert:
        $this->assertListHasValues([1, 2, 3]);
        $this->assertListCount(3);
    }

    public function testOrderIsPreserved(): void
    {
        // Arrange:
        $this->createEmpty();

        // Act:
        $items = [
            '1' => $this->addValue(1),
            '2' => $this->addValue(2),
            '3' => $this->addValue(3),
            '2a' => $this->addValue(2),
            '3a' => $this->addValue(3),
            '2b' => $this->addValue(2),
            '1a' => $this->addValue(1),
        ];

        // Assert:
        $this->assertListHasSameItems([
            $items['1'],
            $items['1a'],
            $items['2'],
            $items['2a'],
            $items['2b'],
            $items['3'],
            $items['3a'],
        ]);
    }

    public function testRemoveFirst(): void
    {
        // Arrange:
        $this->createEmpty();
        $items = [
            '1' => $this->addValue(1),
            '1a' => $this->addValue(1),
            '2' => $this->addValue(2),
            '3' => $this->addValue(3),
        ];

        // Act:
        $removed = $this->removeFirstValue(1);

        // Assert:
        $this->assertTrue($removed);
        $this->assertListHasSameItems([
            $items['1a'],
            $items['2'],
            $items['3'],
        ]);

        // Act:
        $removed = $this->removeFirstValue(4);

        // Assert:
        $this->assertFalse($removed);
    }

    public function testRemoveAll(): void
    {
        // Arrange:
        $this->createWithValues([1, 1, 2, 2, 2, 3]);

        // Act:
        $removed = $this->removeAllValues(2);

        // Assert:
        $this->assertSame(3, $removed);
        $this->assertListHasValues([1, 1, 3]);

        // Act:
        $removed = $this->removeAllValues(1);

        // Assert:
        $this->assertSame(2, $removed);
        $this->assertListHasValues([3]);

        // Act:
        $removed = $this->removeAllValues(3);

        // Assert:
        $this->assertSame(1, $removed);
        $this->assertIsEmpty();

        // Act:
        $removed = $this->removeAllValues(4);

        // Assert:
        $this->assertSame(0, $removed);
    }

    public function testFirstThrowsNoSuchElement(): void
    {
        $this->createEmpty();

        $this->expectException(NoSuchElementException::class);
        $this->list->head();
    }

    public function testLastThrowsNoSuchElement(): void
    {
        $this->createEmpty();

        $this->expectException(NoSuchElementException::class);
        $this->list->tail();
    }

    public function testClear(): void
    {
        // Arrange:
        $this->createWithValues([1, 2, 3]);

        // Act:
        $this->list->clear();

        // Assert:
        $this->assertIsEmpty();
        $this->assertListCount(0);
    }

    public function testWalk(): void
    {
        // Arrange:
        $this->createWithValues([1, 2, 3]);
        $recording = [];

        // Act:
        $this->list->walk(
            function (TestItem $item) use (&$recording) {
                $recording[] = $item->value;
            }
        );

        // Assert:
        $this->assertSame([1, 2, 3], $recording);
    }

    public function testFilter(): void
    {
        // Arrange:
        $this->createWithValues([1, 2, 3, 4, 5]);

        // Act:
        $this->list->filter(fn (TestItem $item) => 0 === $item->value % 2);

        // Assert:
        $this->assertListHasValues([2, 4]);
    }

    public function testCloneEmptyList(): void
    {
        // Arrange:
        $this->createEmpty();
        $clone = clone $this->list;

        // Act:
        $clone->add(new TestItem(1));

        // Assert:
        $this->assertIsEmpty();

        // Act:
        $this->list = $clone;

        // Assert:
        $this->assertListHasValues([1]);
    }

    public function testCloneListWithItems(): void
    {
        // Arrange:
        $this->createWithValues([1, 2, 3]);
        $clone = clone $this->list;

        // Act:
        $clone->removeFirst(new TestItem(1));
        $clone->removeFirst(new TestItem(2));
        $clone->add(new TestItem(4));

        // Assert:
        $this->assertListHasValues([1, 2, 3]);

        // Act:
        $this->list = $clone;

        // Assert:
        $this->assertListHasValues([3, 4]);
    }

    public function testIterator(): void
    {
        // Arrange:
        $this->createWithValues([1, 2, 3]);
        $recording = [];

        // Act:
        foreach ($this->list as $item) {
            $recording[] = $item->value;
        }

        // Assert:
        $this->assertSame([1, 2, 3], $recording);
    }

    private function createEmpty(): void
    {
        $this->list = new LinkedSortedList();
    }

    /**
     * @param array<int> $values
     */
    private function createWithValues(array $values): void
    {
        $items = array_map(fn (int $value) => new TestItem($value), $values);
        $this->createWithItems($items);
    }

    /**
     * @param array<TestItem> $items
     */
    private function createWithItems(array $items): void
    {
        $this->list = new LinkedSortedList($items);
    }

    private function addValue(int $value): TestItem
    {
        $item = new TestItem($value);
        $this->list->add($item);

        return $item;
    }

    private function removeFirstValue(int $value): bool
    {
        return $this->list->removeFirst(new TestItem($value));
    }

    private function removeAllValues(int $value): int
    {
        return $this->list->removeAll(new TestItem($value));
    }

    private function assertIsEmpty(): void
    {
        $this->assertTrue($this->list->isEmpty());
    }

    private function assertIsNotEmpty(): void
    {
        $this->assertFalse($this->list->isEmpty());
    }

    private function assertListCount(int $count): void
    {
        $this->assertCount($count, $this->list);
    }

    /**
     * @param array<TestItem> $items
     */
    private function assertListHasItems(array $items): void
    {
        $this->assertEquals($items, $this->list->toArray());
    }

    /**
     * @param array<TestItem> $items
     */
    private function assertListHasSameItems(array $items): void
    {
        $this->assertSame($items, $this->list->toArray());
    }

    /**
     * @param array<int> $values
     */
    private function assertListHasValues(array $values): void
    {
        $items = array_map(fn (int $value) => new TestItem($value), $values);
        $this->assertListHasItems($items);
    }

    private function assertListHeadValue(int $value): void
    {
        $this->assertSame($value, $this->list->head()->value);
    }

    private function assertListTailValue(int $value): void
    {
        $this->assertSame($value, $this->list->tail()->value);
    }

    private function assertListContainsValue(int $value): void
    {
        $this->assertTrue($this->list->contains(new TestItem($value)));
    }

    private function assertListDoesNotContainValue(int $value): void
    {
        $this->assertFalse($this->list->contains(new TestItem($value)));
    }

    /**
     * @param callable(TestItem): bool $predicate
     */
    private function assertListExists(callable $predicate): void
    {
        $this->assertTrue($this->list->exists($predicate));
    }

    /**
     * @param callable(TestItem): bool $predicate
     */
    private function assertListDoesNotExist(callable $predicate): void
    {
        $this->assertFalse($this->list->exists($predicate));
    }

    private function assertIndexOfValueIs(int $value, int $index): void
    {
        $this->assertSame($index, $this->list->indexOf(new TestItem($value)));
    }

    private function assertIndexOfValueIsNull(int $value): void
    {
        $this->assertNull($this->list->indexOf(new TestItem($value)));
    }

    private function assertListValueOccurrencesCount(int $value, int $count): void
    {
        $this->assertSame($count, $this->list->countOccurrences(new TestItem($value)));
    }
}
