<?php

namespace MKniazuk\SortedLinkedList;

class SortedListFactory
{
    /**
     * @template T of mixed
     *
     * @param iterable<T>       $items
     * @param \Closure(T,T):int $comparator
     *
     * @return SortedList<T>
     */
    public static function create(iterable $items, \Closure $comparator): SortedList
    {
        return new LinkedSortedList($items, $comparator);
    }

    /**
     * @param iterable<int> $integers
     *
     * @return SortedList<int>
     */
    public static function ofIntegers(iterable $integers): SortedList
    {
        return new LinkedSortedList($integers, fn (int $a, int $b) => $a <=> $b);
    }

    /**
     * @param iterable<string> $strings
     *
     * @return SortedList<string>
     */
    public static function ofStrings(iterable $strings): SortedList
    {
        return new LinkedSortedList($strings, fn (string $a, string $b) => strcmp($a, $b));
    }
}
