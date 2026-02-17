<?php

namespace MKniazuk\SortedLinkedList;

/**
 * @implements Comparator<float|int|string>
 */
class ScalarComparator implements Comparator
{
    public function compare(mixed $a, mixed $b): int
    {
        return $a <=> $b;
    }
}
