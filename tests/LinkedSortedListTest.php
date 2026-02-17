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
     * @var LinkedSortedList<int>
     */
    private LinkedSortedList $list;

    public function testSize(): void
    {
        $this->createList();
        $this->assertListCount(0);
        $this->assertIsEmpty();

        $this->createList([1, 2, 3]);
        $this->assertListCount(3);
        $this->assertIsNotEmpty();
    }

    public function testCreateList(): void
    {
        $this->createList();

        $this->assertListCount(0);
        $this->assertIsEmpty();
    }

    public function testCreateWithItems(): void
    {
        $this->createList([1, 2, 3]);

        $this->assertListCount(3);
        $this->assertIsNotEmpty();
    }

    public function testItemsAreSorted(): void
    {
        $this->createList([3, 1, 2]);

        $this->assertListHasValues([1, 2, 3]);
    }

    public function testHeadAndTail(): void
    {
        $this->createList([1, 2, 3]);

        $this->assertListHead(1);
        $this->assertListTail(3);
    }

    public function testContains(): void
    {
        $this->createList([1, 2]);

        $this->assertListContains(1);
        $this->assertListContains(2);
        $this->assertListDoesNotContain(3);
        $this->assertListDoesNotContain(4);
    }

    public function testExists(): void
    {
        $this->createList([1, 2]);

        $this->assertListItemExists(fn (int $item) => 1 === $item);
        $this->assertListItemExists(fn (int $item) => 2 === $item);
        $this->assertListItemDoesNotExist(fn (int $item) => 3 === $item);
        $this->assertListItemDoesNotExist(fn (int $item) => 4 === $item);
    }

    public function testIndexOf(): void
    {
        // Arrange:
        $this->createList([1, 2, 2, 3]);

        // Assert:
        $this->assertIndexOfItemIs(1, 0);
        $this->assertIndexOfItemIs(2, 1);
        $this->assertIndexOfItemIs(3, 3);
        $this->assertIndexOfItemIsNull(4);
    }

    public function testCountOccurrences(): void
    {
        $this->createList([1, 2, 2, 3]);

        $this->assertListItemOccurrencesCount(1, 1);
        $this->assertListItemOccurrencesCount(2, 2);
        $this->assertListItemOccurrencesCount(3, 1);
        $this->assertListItemOccurrencesCount(4, 0);
    }

    public function testAdd(): void
    {
        // Arrange:
        $this->createList();

        // Act:
        $this->addValue(2);
        $this->addValue(1);
        $this->addValue(3);

        // Assert:
        $this->assertListHasValues([1, 2, 3]);
        $this->assertListCount(3);
    }

    public function testRemoveFirst(): void
    {
        // Arrange:
        $this->createList([1, 1, 2, 2, 2, 3]);

        // Act:
        $removed = $this->removeFirstItem(1);

        // Assert:
        $this->assertTrue($removed);
        $this->assertListHasValues([1, 2, 2, 2, 3]);

        // Act:
        $removed = $this->removeFirstItem(2);

        // Assert:
        $this->assertTrue($removed);
        $this->assertListHasValues([1, 2, 2, 3]);

        // Act:
        $removed = $this->removeFirstItem(4);

        // Assert:
        $this->assertFalse($removed);
    }

    public function testRemoveAll(): void
    {
        // Arrange:
        $this->createList([1, 1, 2, 2, 2, 3]);

        // Act:
        $removed = $this->removeAllItems(2);

        // Assert:
        $this->assertSame(3, $removed);
        $this->assertListHasValues([1, 1, 3]);

        // Act:
        $removed = $this->removeAllItems(1);

        // Assert:
        $this->assertSame(2, $removed);
        $this->assertListHasValues([3]);

        // Act:
        $removed = $this->removeAllItems(3);

        // Assert:
        $this->assertSame(1, $removed);
        $this->assertIsEmpty();

        // Act:
        $removed = $this->removeAllItems(4);

        // Assert:
        $this->assertSame(0, $removed);
    }

    public function testFirstThrowsNoSuchElement(): void
    {
        $this->createList();

        $this->expectException(NoSuchElementException::class);
        $this->list->head();
    }

    public function testLastThrowsNoSuchElement(): void
    {
        $this->createList();

        $this->expectException(NoSuchElementException::class);
        $this->list->tail();
    }

    public function testClear(): void
    {
        // Arrange:
        $this->createList([1, 2, 3]);

        // Act:
        $this->list->clear();

        // Assert:
        $this->assertIsEmpty();
        $this->assertListCount(0);
    }

    public function testWalk(): void
    {
        // Arrange:
        $this->createList([1, 2, 3]);
        $recording = [];

        // Act:
        $this->list->walk(
            function (int $item) use (&$recording) {
                $recording[] = $item;
            }
        );

        // Assert:
        $this->assertSame([1, 2, 3], $recording);
    }

    public function testFilter(): void
    {
        // Arrange:
        $this->createList([1, 2, 3, 4, 5]);

        // Act:
        $this->list->filter(fn (int $item) => 0 === $item % 2);

        // Assert:
        $this->assertListHasValues([2, 4]);
    }

    public function testCloneEmptyList(): void
    {
        // Arrange:
        $this->createList();
        $clone = clone $this->list;

        // Act:
        $clone->add(1);

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
        $this->createList([1, 2, 3]);
        $clone = clone $this->list;

        // Act:
        $clone->removeFirst(1);
        $clone->removeFirst(2);
        $clone->add(4);

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
        $this->createList([1, 2, 3]);
        $recording = [];

        // Act:
        foreach ($this->list as $item) {
            $recording[] = $item;
        }

        // Assert:
        $this->assertSame([1, 2, 3], $recording);
    }

    /**
     * @param array<int> $items
     */
    private function createList(array $items = []): void
    {
        /** @var LinkedSortedList<int> $list */
        $list = new LinkedSortedList($items, fn (int $a, int $b) => $a <=> $b);
        $this->list = $list;
    }

    private function addValue(int $item): void
    {
        $this->list->add($item);
    }

    private function removeFirstItem(int $item): bool
    {
        return $this->list->removeFirst($item);
    }

    private function removeAllItems(int $item): int
    {
        return $this->list->removeAll($item);
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
     * @param array<int> $items
     */
    private function assertListHasValues(array $items): void
    {
        $this->assertEquals($items, $this->list->toArray());
    }

    private function assertListHead(int $item): void
    {
        $this->assertSame($item, $this->list->head());
    }

    private function assertListTail(int $item): void
    {
        $this->assertSame($item, $this->list->tail());
    }

    private function assertListContains(int $item): void
    {
        $this->assertTrue($this->list->contains($item));
    }

    private function assertListDoesNotContain(int $item): void
    {
        $this->assertFalse($this->list->contains($item));
    }

    /**
     * @param callable(int): bool $predicate
     */
    private function assertListItemExists(callable $predicate): void
    {
        $this->assertTrue($this->list->exists($predicate));
    }

    /**
     * @param callable(int): bool $predicate
     */
    private function assertListItemDoesNotExist(callable $predicate): void
    {
        $this->assertFalse($this->list->exists($predicate));
    }

    private function assertIndexOfItemIs(int $item, int $index): void
    {
        $this->assertSame($index, $this->list->indexOf($item));
    }

    private function assertIndexOfItemIsNull(int $item): void
    {
        $this->assertNull($this->list->indexOf($item));
    }

    private function assertListItemOccurrencesCount(int $item, int $count): void
    {
        $this->assertSame($count, $this->list->countOccurrences($item));
    }
}
