<?php

namespace MKniazuk\SortedLinkedList;

interface Item
{
    /**
     * @return int $this = $other => exactly 0
     *             $this < $other => negative integer
     *             $this > $other => positive integer
     */
    public function compare(self $other): int;
}
