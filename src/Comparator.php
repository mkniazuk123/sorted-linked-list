<?php

namespace MKniazuk\SortedLinkedList;

/**
 * @template T of mixed
 */
interface Comparator
{
    /**
     * @param T $a
     * @param T $b
     *
     * @return int $this = $other => exactly 0
     *             $this < $other => negative integer
     *             $this > $other => positive integer
     */
    public function compare(mixed $a, mixed $b): int;
}
