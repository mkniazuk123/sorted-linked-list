<?php

namespace MKniazuk\SortedLinkedList;

class SortedListFactory
{
    /**
     * @template T of Item
     *
     * @param iterable<T> $items
     *
     * @return SortedList<T>
     */
    public static function ofItems(iterable $items): SortedList
    {
        return new LinkedSortedList($items);
    }

    public static function ofIntegers(iterable $integers): SortedList
    {
        return new LinkedSortedList(
            array_map(
                static fn (int $integer) => new ScalarItem($integer),
                $integers,
            ),
        );
    }
}
